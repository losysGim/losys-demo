<?php

namespace Losys\CustomerApi\Client;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Header;
use GuzzleHttp\Utils as GuzzleUtils;
use IntlException;
use InvalidArgumentException;
use JsonException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Losys\Demo\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * this class encapsulates a client to access the Losys API
 *
 * it manages...
 * ...authentication
 * ...providing the correct locales
 * ...makes handling of failed requests/error-responses easier
 * ...gathers statistics about every request
 * ...supports you in paging requests
 *
 * feel free to adopt this class to your needs and reuse it
 * in your own projects.
 *
 * you can configure it via an `.env`-file that you must provide
 * in the root folder of this project. create it by copying the
 * provided `.env.example`, edit it, and fill in your personal
 * credentials that you received from support@losys.ch
 */
class LosysClient
{
    /*
     * settings red from the .env file
     */
    private string                  $losys_instance_uri;
    private string                  $losys_client_id;
    private ?string                 $losys_client_secret;
    private ?string                 $losys_client_app;

    /*
     * variables acting as cache to store generated values
     */
    private ?AccessTokenInterface   $access_token = null;

    private ?Client                 $client = null;
    private ?string                 $locale = null;

    private const string            DEFAULT_ACCEPT_HTML = 'text/html,application/xhtml+xml,application/xml;q=0.9,application/json;q=0.8,*/*;q=0.7';
    public const string             DEFAULT_LOCALE = 'de';

    private array                   $additionalGuzzleOptions = [];

    private ?float                  $last_request_start = null;
    private ?float                  $last_duration_seconds = null;
    private ?int                    $last_size_bytes = null;
    private ?array                  $last_response_headers = null;
    private ?array                  $last_request_info = null;
    private ?array                  $last_request_info_filtered = null;

    private SessionHandlerInterface $session;

    private ?string                 $auto_code_code = null;
    private ?string                 $auto_code_state = null;


    public function __construct()
    {
        $this->session = new SessionHandler();
        $this->loadConfiguration();
    }

    /**
     * reads the .env file into our setting-variables
     *
     * @return void
     *
     * @throws JsonException
     */
    protected function loadConfiguration(): void
    {
        $env = Dotenv::createArrayBacked(dirname(__DIR__, 3));
        $config = $env->load();

        $env->required(['LOSYS_INSTANCE', 'LOSYS_CLIENT_ID'])->notEmpty();

        $this->losys_instance_uri  = $config['LOSYS_INSTANCE'];
        $this->losys_client_id     = $config['LOSYS_CLIENT_ID'];
        $this->losys_client_secret = ($config['LOSYS_CLIENT_SECRET'] ?? null) ?: null;
        $this->losys_client_app    = ($config['LOSYS_CLIENT_APP'] ?? null) ?: null;

        if ((empty($this->losys_client_secret) && empty($this->losys_client_app))
            || (!empty($this->losys_client_secret) && !empty($this->losys_client_app)))
            throw new JsonException(
                'you must either configure "LOSYS_CLIENT_SECRET" (to use the "Client Credentials Flow") or "LOSYS_CLIENT_APP" (to use the "Authorization Code Flow") - but neither both nor none of them.'
            );

        if (!str_ends_with($this->losys_instance_uri, '/'))
            $this->losys_instance_uri .= '/';

        if ($this->losys_client_app
            && !str_ends_with($this->losys_client_app, '/'))
            $this->losys_client_app .= '/';

        if (array_key_exists('GUZZLE_OPTIONS', $config)
            && !empty($opt = $config['GUZZLE_OPTIONS']))
        {
            try
            {
                if (!is_array($opt = json_decode($opt, true, flags: JSON_THROW_ON_ERROR)))
                    throw new JsonException('expected: array');
            } catch (JsonException $e) {
                throw new JsonException(
                    "your .env-file-setting \"GUZZLE_OPTIONS\" is invalid: {$e->getMessage()}",
                    $e->getCode(),
                    $e
                );
            }

            $this->additionalGuzzleOptions = $opt;
        }
    }

    /**
     * tries to get the locale from the browser of the client
     * accessing our website.
     *
     * @return string       e.g. 'de' or 'en-US
     */
    protected function getLocale(): string
    {
        if ($this->locale)
            return $this->locale;

        try {
            $this->locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        } catch (IntlException) {
            $this->locale = null;
        }

        if (!$this->locale)
            $this->locale = self::DEFAULT_LOCALE;

        if (preg_match('/^[A-Za-z]{2}_[A-Za-z]{2}$/', $this->locale))
            $this->locale = str_replace('_', '-', $this->locale);

        return $this->locale;
    }

    public function useAuthorizationCodeFlow(): bool
    {
        return !empty($this->losys_client_app);
    }

    private function getProvider(): LosysProvider
    {
        return new LosysProvider(array_merge(
            $this->useAuthorizationCodeFlow()
                ? [
                'clientId'                => $this->losys_client_id,
                'redirectUri'             => $this->losys_client_app . 'ac_pkce.php',
                'urlAccessToken'          => $this->losys_instance_uri . 'oauth/token',
                'urlAuthorize'            => $this->losys_instance_uri . 'oauth/authorize',
                'urlResourceOwnerDetails' => '',
                'pkceMethod'              => AbstractProvider::PKCE_METHOD_S256,
            ]
                : [
                'clientId'                => $this->losys_client_id,
                'clientSecret'            => $this->losys_client_secret,
                'urlAccessToken'          => $this->losys_instance_uri . 'oauth/token',
                'urlAuthorize'            => '',
                'urlResourceOwnerDetails' => '',
            ],
            $this->additionalGuzzleOptions
        ));
    }

    /**
     * @param string|null $code
     * @param string|null $state
     * @return void
     *
     * @throws AuthorizationFailedException
     * @throws GuzzleException
     * @throws IdentityProviderException
     * @throws RedirectException
     */
    public function setAuthorizationCodeFlowState(?string $code,
                                                  ?string $state): void
    {
        if (!$this->useAuthorizationCodeFlow())
            throw new InvalidArgumentException('not configured for Authorization Code Flow. set LOSYS_CLIENT_APP in your .env file.');

        $this->auto_code_code = $code;
        $this->auto_code_state = $state;

        $this->getAccessToken();
    }

    /**
     * @return AccessTokenInterface
     *
     * @throws GuzzleException
     * @throws IdentityProviderException
     * @throws AuthorizationFailedException
     * @throws RedirectException
     */
    private function gatherAccessToken(): AccessTokenInterface
    {
        $provider = $this->getProvider();

        if ($this->useAuthorizationCodeFlow()) {
            if (empty($this->auto_code_code)) {
                // phase 1: start the authorization flow
                $authorizationUrl = $provider->getAuthorizationUrl();
                $this->session->set(SessionVariableEnum::State, $provider->getState());
                $this->session->set(SessionVariableEnum::PkceCodeVerifier, $provider->getPkceCode());
                throw new RedirectException($authorizationUrl);

            } elseif (empty($this->auto_code_state)
                      || empty($state = $this->session->get(SessionVariableEnum::State))
                      || ($state !== $this->auto_code_state)) {
                // phase 2: error / CSRF-attack
                $this->session->clear();
                throw new AuthorizationFailedException();
            } else {
                // phase 2: get the authorization code
                try {
                    $provider->setPkceCode($this->session->get(SessionVariableEnum::PkceCodeVerifier));

                    // Try to get an access token using the authorization code grant.
                    return $provider->getAccessToken('authorization_code', [
                        'code' => $this->auto_code_code
                    ]);
                } finally {
                    $this->session->clear();
                }
            }
        } else
            return $provider->getAccessToken('client_credentials');
    }

    /**
     * logs in to the Losys-platform and returns the acquired token.
     *
     * @return AccessTokenInterface
     *
     * @throws IdentityProviderException
     * @throws GuzzleException
     * @throws AuthorizationFailedException
     * @throws RedirectException
     */
    protected function getAccessToken(): AccessTokenInterface
    {
        return $this->access_token ??= $this->gatherAccessToken();
    }

    /**
     * calls the Losys API and returns the resulting answer
     *
     * @param string $uri                     relative uri of the api-method to call.
     *                                        should normally not start with a '/'.
     *                                        example: 'api/customer/project'
     * @param array  $data                    data to send to the api-method
     * @param string $httpMethod              http-method to use
     *                                        example: 'GET', 'PUT', 'POST' or 'DELETE'
     * @param string $expectedContentType     the mime-type of the response that you
     *                                        expect the api to return.
     *                                        use the internal alias 'html' if you want text/html or something else.
     *                                        example: 'application/json' or 'text/html' or 'application/pdf'
     * @param array  $guzzleRequestOptions    additional options for the guzzle http-client
     *                                        see https://docs.guzzlephp.org/en/stable/request-options.html
     *                                        example ['http_errors' => true]
     *
     * @return string|int|array|ResponseInterface will return the body of the api's response.
     *                                        if you set `$guzzleRequestOptions = ['http_errors' => true]`
     *                                        and the api-call failed it will return the `ResponseInterface`
     *
     * @throws IdentityProviderException
     * @throws GuzzleException
     */
    public function callApi(string $uri,
                            array  $data = [],
                            string $httpMethod = 'GET',
                            string $expectedContentType = 'application/json',
                            array  $guzzleRequestOptions = []): string|int|array|ResponseInterface
    {
        if (!$this->client)
            $this->client = new Client([
                'base_uri' => $this->losys_instance_uri,
                'timeout'  => 30,
                'cookies'  => true
            ]);

        if ($httpMethod === 'GET') {
            $options['query'] = $data;
        } else
            $options['json'] = $data;

        $options = array_merge_recursive($options,
            [
                'headers' => [
                    'Authorization'   => 'Bearer ' . $this->getAccessToken()->getToken(),
                    'Accept'          => $expectedContentType === 'html' ? self::DEFAULT_ACCEPT_HTML : $expectedContentType,
                    'Accept-Language' => $this->getLocale(),
                    'Accept-Encoding' => 'gzip, deflate'
                ]
            ],
            $guzzleRequestOptions,
            $this->additionalGuzzleOptions
        );

        $this->resetLastResponseStatistics();
        $this->last_request_info = [
            'uri'     => $uri,
            'method'  => $httpMethod,
            'options' => $options,
        ];
        $this->last_request_info_filtered = null;

        try {
            $response =
                $this->client->request(
                    $httpMethod,
                    $uri,
                    $options
                );

            $this->setLastResponseStatistics($response);
        } catch (RequestException $e) {
            $this->setLastResponseStatistics($response = $e->getResponse());

            if (($e instanceof BadResponseException)
                && $response
                && $this->isJsonResponse($response)
                && strlen($body = $response->getBody()->getContents())
                && is_array($error = GuzzleUtils::jsonDecode($body, true)))
            {
                if (array_key_exists('error', $error)
                    && is_array($error['error'])
                    && array_key_exists('message', $error['error']))
                {
                    throw new LosysBackendException($error['error'], $e);
                } else {
                    throw new BackendErrorResponseException($error, $e);
                }
            }

            throw $e;
        } finally {
            $this->setLastResponseStatistics(null);
        }

        if (($response->getStatusCode() >= 200)
            && ($response->getStatusCode() < 300))
        {
            $body = $response->getBody()->getContents();

            return
                $this->isJsonResponse($response)
                    ? GuzzleUtils::jsonDecode($body, true)
                    : $body;
        }

        return $response;
    }

    private function resetLastResponseStatistics(): void
    {
        $this->last_duration_seconds = $this->last_size_bytes = $this->last_response_headers = null;
        $this->last_request_start = microtime(true);
    }

    private function setLastResponseStatistics(?ResponseInterface $response): void
    {
        if (!is_null($this->last_request_start)
            && is_null($this->last_duration_seconds))
            $this->last_duration_seconds = (microtime(true) - $this->last_request_start);

        if (is_null($response))
            return;

        $this->last_size_bytes =
            $response->getBody()->getSize()
            ?? strlen($response->getBody()->getContents() ?? '');

        $this->last_response_headers =
            array_combine(
                array_map(fn($name) => strtolower($name), array_keys($headers = $response->getHeaders())),
                $headers
            );
    }

    private function isJsonResponse(ResponseInterface $response): bool
    {
        return
            array_key_exists(0, $mimeType = Header::parse($response->getHeader('Content-Type')))
            && is_array($first = $mimeType[0])
            && array_key_exists(0, $first)
            && preg_match('%^application/json($|[+;])%i', $first[0]);
    }

    public function getLastResponseHeader(string $header): ?string
    {
        if (is_null($this->last_response_headers)
            || !array_key_exists($key = strtolower($header), $this->last_response_headers))
            return null;

        return implode('', $this->last_response_headers[$key]);
    }

    public function getLastRequestInfo(): array
    {
        return $this->last_request_info_filtered ??= Utils::array_hide_secrets($this->last_request_info);
    }

    /**
     * @return array<string, mixed>
     */
    public function getLastResponseStatistics(): array
    {
        $paginatorInfo = array_filter(
            $this->last_response_headers ?? [],
            fn($value, $name) => str_starts_with(strtolower($name), 'x-paginator-'),
            ARRAY_FILTER_USE_BOTH
        );
        $paginatorInfo = array_combine(
            array_map(fn($name) => substr($name, strlen('x-paginator-')), array_keys($paginatorInfo)),
            array_map(fn($value) => implode('', $value), $paginatorInfo)
        );

        return array_merge(
            [
                'duration'      => is_null($this->last_duration_seconds) ? null : number_format($this->last_duration_seconds, 2) . ' seconds',
                'response-size' => is_null($this->last_size_bytes) ? null : number_format($this->last_size_bytes / 1024, 1, '.', '\'') . ' kB',
                'request-id'    => $this->getLastResponseHeader('X-Request-Id'),
            ],
            $paginatorInfo
        );
    }
}