<?php

namespace LemonSqueezy\Batch;

use LemonSqueezy\Batch\Operations\BatchOperation;

/**
 * Result container for batch operations
 *
 * Holds information about successful and failed operations,
 * along with execution statistics.
 */
class BatchResult
{
    private array $successful = [];
    private array $failed = [];
    private float $executionTime = 0.0;

    /**
     * Add a successful operation result
     *
     * @param BatchOperation $operation The operation that succeeded
     * @param mixed $result The result from the API
     * @param int $statusCode The HTTP status code
     */
    public function addSuccess(
        BatchOperation $operation,
        mixed $result,
        int $statusCode = 200
    ): self {
        $this->successful[] = [
            'operation' => $operation,
            'result' => $result,
            'statusCode' => $statusCode,
        ];

        return $this;
    }

    /**
     * Add a failed operation result
     *
     * @param BatchOperation $operation The operation that failed
     * @param string $error The error message
     * @param int $statusCode The HTTP status code
     * @param ?array $errorDetails Additional error details from API
     */
    public function addFailure(
        BatchOperation $operation,
        string $error,
        int $statusCode = 400,
        ?array $errorDetails = null
    ): self {
        $this->failed[] = [
            'operation' => $operation,
            'error' => $error,
            'statusCode' => $statusCode,
            'errorDetails' => $errorDetails,
        ];

        return $this;
    }

    /**
     * Set the total execution time
     */
    public function setExecutionTime(float $time): self
    {
        $this->executionTime = $time;
        return $this;
    }

    /**
     * Check if all operations were successful
     */
    public function wasSuccessful(): bool
    {
        return empty($this->failed);
    }

    /**
     * Check if there were any failures
     */
    public function hasFailures(): bool
    {
        return !empty($this->failed);
    }

    /**
     * Get all successful operations
     *
     * @return array Array of successful operation results
     */
    public function getSuccessful(): array
    {
        return $this->successful;
    }

    /**
     * Get all failed operations
     *
     * @return array Array of failed operation results
     */
    public function getFailed(): array
    {
        return $this->failed;
    }

    /**
     * Get the number of successful operations
     */
    public function getSuccessCount(): int
    {
        return count($this->successful);
    }

    /**
     * Get the number of failed operations
     */
    public function getFailureCount(): int
    {
        return count($this->failed);
    }

    /**
     * Get the total number of operations
     */
    public function getTotalCount(): int
    {
        return $this->getSuccessCount() + $this->getFailureCount();
    }

    /**
     * Get execution time in seconds
     */
    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    /**
     * Get summary statistics
     */
    public function getSummary(): array
    {
        return [
            'totalRequested' => $this->getTotalCount(),
            'successCount' => $this->getSuccessCount(),
            'failureCount' => $this->getFailureCount(),
            'successRate' => $this->getTotalCount() > 0
                ? round(($this->getSuccessCount() / $this->getTotalCount()) * 100, 2)
                : 100.0,
            'executionTime' => $this->executionTime,
        ];
    }
}
