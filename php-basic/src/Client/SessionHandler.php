<?php

namespace Losys\CustomerApi\Client;

/**
 * class to handle session state
 *
 * feel free to override this to use your own state-storage.
 *
 * this classes uses PHPs internal session-handler.
 */
class SessionHandler
    implements SessionHandlerInterface
{
    public function get(SessionVariableEnum $variable): mixed
    {
        return $_SESSION[$variable->value] ?? null;
    }

    public function set(SessionVariableEnum $variable, mixed $value): void
    {
        if (is_null($value)) {
            if (isset($_SESSION[$variable->value]))
                unset($_SESSION[$variable->value]);
        } else {
            $_SESSION[$variable->value] = $value;
        }
    }

    public function clear(): void
    {
        foreach(SessionVariableEnum::cases() as $case)
            $this->set($case, null);
    }
}