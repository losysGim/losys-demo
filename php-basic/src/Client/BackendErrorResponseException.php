<?php

namespace Losys\CustomerApi\Client;

use GuzzleHttp\Exception\BadResponseException;

class BackendErrorResponseException
    extends BadResponseException
{
    private array $data;

    public function __construct(array $data,
                                BadResponseException $previous)
    {
        $this->data = $data;
        parent::__construct(
            LosysBackendException::getMessageInternal($data, $previous),
            $previous->getRequest(),
            $previous->getResponse(),
            $previous,
            $previous->getHandlerContext()
        );
    }

    public function getData(): array
    {
        return $this->data;
    }
}