<?php

namespace Losys\CustomerApi\Client;

use Exception;

class AuthorizationFailedException
    extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'the authorization failed. check that your session-handler is setup '
            . 'correctly and there is no proxy/anti-virus intercepting calls between this '
            . 'instance and the losys-backend.',
            401
        );
    }
}