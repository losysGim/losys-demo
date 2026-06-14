<?php

namespace Losys\CustomerApi\Client;

use InvalidArgumentException;
use JsonException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * a dependency-free, file-based {@see TokenStoreInterface}.
 *
 * each token is stored as a small JSON file produced by
 * {@see AccessToken::jsonSerialize()} and reconstructed via `new AccessToken()`.
 * a token is a bearer-credential, therefore the files...
 *   ...live OUTSIDE the web-docroot (`php-basic/var/tokens`, not `public/`),
 *      so they can never be requested over HTTP,
 *   ...are written with `0600` permissions (owner read/write only),
 *   ...are written atomically (temp-file + rename) under an exclusive lock,
 *      so a half-written file can never be read.
 *
 * the file-name is a hash of the caller-provided key (flow + instance +
 * client-id), so different configurations/instances never collide.
 */
class FileTokenStore
    implements TokenStoreInterface
{
    private string $directory;

    /**
     * @param string|null $directory  where to store the token files.
     *                                 defaults to `php-basic/var/tokens`.
     */
    public function __construct(?string $directory = null)
    {
        // dirname(__DIR__, 2) === the `php-basic` folder (this file lives in
        // php-basic/src/Client).
        $this->directory = rtrim($directory ?? dirname(__DIR__, 2) . '/var/tokens', '/\\');
    }

    public function load(string $key): ?AccessTokenInterface
    {
        $file = $this->fileForKey($key);

        if (!is_file($file) || !is_readable($file))
            return null;

        $raw = @file_get_contents($file);
        if ($raw === false || $raw === '')
            return null;

        try {
            $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        if (!is_array($data) || empty($data['access_token']))
            return null;

        try {
            // round-trips cleanly: jsonSerialize() writes the absolute
            // `expires` timestamp, which the AccessToken constructor restores.
            return new AccessToken($data);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function save(string $key, AccessTokenInterface $token): void
    {
        if (!is_dir($this->directory))
            @mkdir($this->directory, 0700, true);

        $file = $this->fileForKey($key);
        $tmp  = $file . '.' . getmypid() . '.tmp';

        try {
            $json = json_encode($token->jsonSerialize(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        } catch (JsonException) {
            return;
        }

        if (@file_put_contents($tmp, $json, LOCK_EX) === false)
            return;

        @chmod($tmp, 0600);

        // atomic replace: readers see either the old or the new file, never a
        // partial write.
        if (!@rename($tmp, $file))
            @unlink($tmp);
    }

    public function clear(string $key): void
    {
        $file = $this->fileForKey($key);

        if (is_file($file))
            @unlink($file);
    }

    private function fileForKey(string $key): string
    {
        return $this->directory . '/' . hash('sha256', $key) . '.json';
    }
}
