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
use JsonException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Losys\Demo\Utils;
use Psr\Http\Message\ResponseInterface;

class LosysClient
{
    /*
     * settings red from the .env file
     */
    private string                $losys_instance_uri;
    private string                $losys_client_id;
    private string                $losys_client_secret;

    /*
     * variables acting as cache to store generated values
     */
    private ?AccessTokenInterface $access_token = null;

    private ?Client               $client = null;
    private ?string               $locale = null;

    private const string          DEFAULT_ACCEPT_HTML = 'text/html,application/xhtml+xml,application/xml;q=0.9,application/json;q=0.8,*/*;q=0.7';
    public const string           DEFAULT_LOCALE = 'de';

    private array                 $additionalGuzzleOptions = [];

    private ?float                $last_request_start = null;
    private ?float                $last_duration_seconds = null;
    private ?int                  $last_size_bytes = null;
    private ?array                $last_response_headers = null;
    private ?array                $last_request_info = null;
    private ?array                $last_request_info_filtered = null;


    public function __construct()
    {
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

        $env->required(['LOSYS_INSTANCE', 'LOSYS_CLIENT_ID', 'LOSYS_CLIENT_SECRET'])->notEmpty();

        $this->losys_instance_uri  = $config['LOSYS_INSTANCE'];
        $this->losys_client_id     = $config['LOSYS_CLIENT_ID'];
        $this->losys_client_secret = $config['LOSYS_CLIENT_SECRET'];

        if (!str_ends_with($this->losys_instance_uri, '/'))
            $this->losys_instance_uri .= '/';

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

    /**
     * logs in to the Losys-platform and returns the acquired token.
     *
     * @return AccessTokenInterface
     *
     * @throws IdentityProviderException|GuzzleException
     */
    protected function getAccessToken(): AccessTokenInterface
    {
        if ($this->access_token)
            return $this->access_token;

        $client = new LosysProvider(array_merge([
            'clientId'                => $this->losys_client_id,
            'clientSecret'            => $this->losys_client_secret,
            'urlAccessToken'          => $this->losys_instance_uri . 'oauth/token',
            'urlAuthorize'            => '',
            'urlResourceOwnerDetails' => ''
        ], $this->additionalGuzzleOptions));

        return $this->access_token = $client->getAccessToken('client_credentials');
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