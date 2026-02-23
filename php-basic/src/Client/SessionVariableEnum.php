<?php

namespace Losys\CustomerApi\Client;

enum SessionVariableEnum: string
{
    case State = 'oauth_state';
    case PkceCodeVerifier = 'oauth_pkce_code_verifier';
}
