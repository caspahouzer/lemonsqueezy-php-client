<?php

namespace LemonSqueezy\Exception;

/**
 * Base exception for all LemonSqueezy API errors
 */
class LemonSqueezyException extends \Exception
{
    protected ?array $response = null;

    /**
     * @param string $message
     * @param int $code
     * @param ?array $response The API response data
     * @param ?\Throwable $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?array $response = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Get the API response data
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }
}
