<?php

namespace Losys\CustomerApi\Client;

use Exception;

class RedirectException
    extends Exception
{
    public function __construct(protected(set) readonly string $redirectToUri)
    {
        parent::__construct("please redirect to {$this->redirectToUri}");
    }
}