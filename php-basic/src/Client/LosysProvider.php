<?php

namespace Losys\CustomerApi\Client;

use League\OAuth2\Client\Provider\GenericProvider;

class LosysProvider
    extends GenericProvider
{
    protected function getAllowedClientOptions(array $options): array
    {
        return array_merge(
            parent::getAllowedClientOptions($options),
            [
                'headers'
            ]
        );
    }
}