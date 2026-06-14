<?php

namespace Losys\CustomerApi\Client;

use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * persists an OAuth access-token (and, for the Authorization Code Flow, its
 * refresh-token) across requests / CLI-runs so that a token can be reused
 * until it expires instead of requesting a brand-new one on every single run.
 *
 * why this matters:
 *   without persistence a per-request web-context (or a "run once per minute"
 *   cron/n8n job) creates a fresh LosysClient on every run, which triggers a
 *   new token-issuance every time. that needlessly hammers the auth-server
 *   (load/cost) for tokens that are valid for days.
 *
 * feel free to override this with your own storage (Redis, database, a
 * per-end-user store for multi-user Authorization-Code apps, ...). the
 * file-based default {@see FileTokenStore} needs no infrastructure and can
 * be copied 1:1 into your own project.
 */
interface TokenStoreInterface
{
    /**
     * returns the stored token for the given key, or null if there is none
     * (or it could not be read/parsed).
     */
    public function load(string $key): ?AccessTokenInterface;

    /**
     * stores (or replaces) the token under the given key.
     */
    public function save(string $key, AccessTokenInterface $token): void;

    /**
     * removes the stored token for the given key (e.g. after the server
     * rejected it with a 401).
     */
    public function clear(string $key): void;
}
