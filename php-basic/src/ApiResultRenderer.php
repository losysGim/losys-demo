<?php

namespace Losys\Demo;

use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Losys\CustomerApi\Client\LosysBackendException;
use Losys\CustomerApi\Client\LosysClient;
use Throwable;

class ApiResultRenderer
{
    private ?LosysClient $_client;
    public LosysClient $client {
        get => $this->_client ??= new LosysClient();
    }

    public function getProjectsFromApiAndRenderResults(array  $parameters = [],
                                                       string $afterResultTable = ''): string
    {
        try
        {
            $data = $this->client->callApi('api/customer/project', $parameters);

            // show the input-parameters
            $result = '<h3>This is the API-request we send</h3>'
                . '<pre>'
                . json_encode(
                    $this->client->getLastRequestInfo(),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
                . '</pre>';

            // render the projects
            $result .=
                '<h3>These projects were returned</h3>'
                . '<table><thead><tr>'
                . implode('', array_map(fn($value) => '<th>' . htmlentities($value) . '</th>', [
                    'Title',
                    'Canton',
                    'Year',
                    '# images'
                ]))
                . '</tr></thead><tbody>';

            foreach($data as $project)
                $result .= '<tr>'
                    . implode('', array_map(fn ($value) => '<td>' . htmlentities($value??'') . '</td>', [
                        $project['title'],
                        $project['canton'],
                        $project['yearOfCompletion'],
                        array_key_exists('project_images', $project) ? count($project['project_images']) : '???'
                    ]))
                    . '</tr>';

            return $result .
                '</tbody></table>'
                .  $afterResultTable

                // show the received JSON
                . '<h3>this is the raw-data the api really returned</h3>'
                . '<pre>'
                . json_encode(
                    $data,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
                . '</pre>'

                . '<small>API request statistics: '
                . implode(
                    ', ',
                    array_map(
                        fn($value, $key) => "{$key}: {$value}",
                        $stats = $this->client->getLastResponseStatistics(),
                        array_keys($stats)
                    )
                )
                . '</small>';

        } catch (Throwable $e) {
            return self::renderExceptionInfo($e);
        }
    }

    public static function renderExceptionInfo(Throwable $e): string
    {
        return match (get_class($e)) {
            // indicates the Losys API returned an error
            LosysBackendException::class =>
                "<p class='error'>\"{$e->getErrorType()}\"-Error with Code #{$e->getErrorCode()} from Losys-API: {$e->getMessage()}<br>"
                . implode(
                    '<br>',
                    array_combine(
                        $fieldNames = array_keys($fieldErrors = $e->getFieldErrors()),
                        array_map(
                            fn($errors, $fieldName) => "<strong>{$fieldName}</strong>: " . implode('; ', $errors) . '<br>',
                            $fieldErrors,
                            $fieldNames
                        )))
                . "<br>If you need to contact Losys-Support on this error please provide your Request-Id \"{$e->getRequestId()}\"</p>",

            // indicates there is a problem connecting to the server or a network-problem (e.g. no internet-access)
            GuzzleException::class =>
                "<p class='error'>Network-/Transmission-Error: {$e->getMessage()}</p>",

            // indicates there is an error authenticating against the Losys API
            IdentityProviderException::class =>
                "<p class='error'>You could not be authenticated (check LOSYS_CLIENT_ID and LOSYS_CLIENT_SECRET in your .env-file): {$e->getMessage()}</p>",

            default =>
                '<p class="error">Error "' . get_class($e) . "\": {$e->getMessage()}</p>",
        };
    }
}