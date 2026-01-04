<?php

namespace LemonSqueezy\Exception;

/**
 * Exception for validation errors (invalid input parameters)
 */
class ValidationException extends ClientException
{
    protected ?array $errors = null;

    /**
     * @param string $message
     * @param int $code
     * @param ?array $errors Validation errors detail
     * @param ?array $response The API response data
     * @param ?\Throwable $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?array $errors = null,
        ?array $response = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $response, $previous);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
