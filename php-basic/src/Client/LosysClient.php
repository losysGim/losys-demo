<?php

namespace Losys\CustomerApi\Client;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Header;
use GuzzleHttp\Utils;
use IntlException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;

class LosysClient {
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


    public function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * reads the .env file into our setting-variables
     *
     * @return void
     */
    protected function loadConfiguration(): void
    {
        $env = Dotenv::createArrayBacked(dirname(dirname(dirname(__DIR__))));
        $config = $env->load();

        $env->required(['LOSYS_INSTANCE', 'LOSYS_CLIENT_ID', 'LOSYS_CLIENT_SECRET'])->notEmpty();

        $this->losys_instance_uri  = $config['LOSYS_INSTANCE'];
        $this->losys_client_id     = $config['LOSYS_CLIENT_ID'];
        $this->losys_client_secret = $config['LOSYS_CLIENT_SECRET'];

        if (!str_starts_with($this->losys_instance_uri, '/'))
            $this->losys_instance_uri .= '/';
    }

    /**
     * tries to get the locale from the browser of the client
     * accessing our website.
     *
     * @return string       e.g. 'de' or 'en_US
     */
    protected function getLocale(): string
    {
        if ($this->locale)
            return $this->locale;

        try {
            $this->locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        } catch (IntlException $exception) {
            $this->locale = null;
        }

        if (!$this->locale)
            $this->locale = 'de';

        return $this->locale;
    }

    /**
     * logs in to the Losys-platform and returns the acquired token.
     *
     * @return AccessTokenInterface
     *
     * @throws IdentityProviderException
     */
    protected function getAccessToken(): AccessTokenInterface
    {
        if ($this->access_token)
            return $this->access_token;

        $client = new GenericProvider([
            'clientId'                => $this->losys_client_id,
            'clientSecret'            => $this->losys_client_secret,
            'urlAccessToken'          => $this->losys_instance_uri . 'oauth/token',
            'urlAuthorize'            => '',
            'urlResourceOwnerDetails' => ''
        ]);

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
     *                                        example: 'application/json' or 'text/html' or 'application/pdf'
     * @param array  $guzzleRequestOptions    additional options for the guzzle http-client
     *                                        see https://docs.guzzlephp.org/en/stable/request-options.html
     *                                        example ['http_errors' => true]
     *
     * @return string|array|ResponseInterface will return the body of the api's response.
     *                                        if you set $guzzleRequestOptions = ['http_errors' => true]
     *                                        and the api-call failed it will return the ResponseInterface
     *
     * @throws IdentityProviderException
     * @throws GuzzleException
     */
    public function callApi(string $uri,
                            array  $data = [],
                            string $httpMethod = 'GET',
                            string $expectedContentType = 'application/json',
                            array  $guzzleRequestOptions = [])
    {
        if (!$this->client)
            $this->client = new Client([
                'base_uri' => $this->losys_instance_uri,
                'timeout'  => 30
            ]);

        if ($httpMethod === 'GET') {
            $options['query'] = $data;
        } else
            $options['json'] = $data;

        $options = array_merge_recursive($options,
            [
                'headers' => [
                    'Authorization'   => 'Bearer ' . $this->getAccessToken()->getToken(),
                    'Accept'          => $expectedContentType,
                    'Accept-Language' => $this->getLocale()
                ]
            ],
            $guzzleRequestOptions
        );

        $response =
            $this->client->request(
                $httpMethod,
                $uri,
                $options
            );

        if (($response->getStatusCode() >= 200)
            && ($response->getStatusCode() < 300)) {
            if (is_array($mimeType = Header::parse($response->getHeader('Content-Type') ?? ''))
                && array_key_exists(0, $mimeType)
                && is_array($first = $mimeType[0])
                && array_key_exists(0, $first)) {
                $mimeType = $first[0];
            } else
                $mimeType = null;

            $body = $response->getBody()->getContents();

            return
                ($mimeType == 'application/json')
                    ? Utils::jsonDecode($body, true)
                    : $body;
        }

        return $response;
    }
}