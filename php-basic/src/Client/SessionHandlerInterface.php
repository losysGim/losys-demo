<?php

namespace Losys\CustomerApi\Client;

interface SessionHandlerInterface
{
    public function get(SessionVariableEnum $variable): mixed;
    public function set(SessionVariableEnum $variable, mixed $value): void;
    public function clear(): void;
}