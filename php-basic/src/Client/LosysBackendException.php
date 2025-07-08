<?php

namespace Losys\CustomerApi\Client;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Header;

class LosysBackendException
    extends BadResponseException
{
    private array $error;

    public function __construct(array $error,
                                BadResponseException $previous)
    {
        $this->error = $error;
        parent::__construct(
            $this->getMessageInternal($error, $previous),
            $previous->getRequest(),
            $previous->getResponse(),
            $previous,
            $previous->getHandlerContext()
        );
    }

    protected static function getMessageInternal(array $error, BadResponseException $previous): string
    {
        if (!array_key_exists('message', $error)
            || !($message = $error['message']))
            return $previous->getResponse()->getReasonPhrase() ?: 'unknown error from Losys backend';
        return $message;
    }

    public function getErrorDetails(): array
    {
        return $this->error;
    }

    /**
     * a string indicating the type of error that occurred.
     * e.g. "Illuminate\Validation\ValidationException"
     *
     * beware:
     *   the types returned here may change (after updates of
     *   the Losys code base) without notice.
     *
     * @return string
     */
    public function getErrorType(): string
    {
        return
            (array_key_exists('type', $this->error)
             && $this->error['type'])
                ? $this->error['type']
                : "error_{$this->getErrorCode()}";
    }

    public function getErrorCode(): int
    {
        return
            (array_key_exists('code', $this->error)
                && is_int($this->error['code'])
                && ($code = ((int) $this->error['code'])))
                ? $code
                : $this->getResponse()->getStatusCode();
    }

    /**
     * if the error is caused by one or more of the input-values
     * that you provide the input-fields causing errors are
     * returned here.
     *
     * example:
     *   if you call 'api/customer/project/html/box?offset=xxx'
     *   the API will return an error with
     *   type = 'Illuminate\Validation\ValidationException'
     *   and field_errors = ['offset' => [0 => 'offset muss eine ganze Zahl sein.']]
     *
     * @return array
     */
    public function getFieldErrors(): array
    {
        return
            array_key_exists('field_errors', $this->error)
            && is_array($field_errors = $this->error['field_errors'])
                ? $field_errors
                : [];
    }

    /**
     * a request-id is automatically added to every request. Losys support
     * can use this ID to gather details about your specific problem.
     *
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return join('', $this->getResponse()->getHeader('X-Request-Id')) ?: null;
    }
}