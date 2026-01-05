<?php

namespace LemonSqueezy\Exception;

use LemonSqueezy\Batch\BatchResult;

/**
 * Exception thrown during batch operation processing
 *
 * This exception is thrown when a batch operation encounters critical errors
 * that prevent further execution.
 */
class BatchException extends LemonSqueezyException
{
    /**
     * Create a new batch exception
     *
     * @param string $message The error message
     * @param int $code The error code
     * @param ?BatchResult $batchResult The batch result with partial data
     * @param ?array $response The API response data
     * @param ?\Throwable $previous The previous exception
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        private ?BatchResult $batchResult = null,
        ?array $response = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $response, $previous);
    }

    /**
     * Get the batch result (may contain partial data)
     */
    public function getBatchResult(): ?BatchResult
    {
        return $this->batchResult;
    }
}
