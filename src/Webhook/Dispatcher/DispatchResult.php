<?php

namespace LemonSqueezy\Webhook\Dispatcher;

/**
 * Result object from event dispatch operation
 *
 * Contains information about handler execution including successes,
 * failures, and overall dispatch status.
 */
class DispatchResult
{
    /**
     * @var array<int, array{handler: mixed, error: \Throwable}>
     */
    private array $failures = [];

    /**
     * @var array<int, mixed>
     */
    private array $successes = [];

    /**
     * Create a new DispatchResult
     *
     * @param string $eventType The event type that was dispatched
     * @param int $handlerCount Total number of handlers that were executed
     */
    public function __construct(
        private string $eventType,
        private int $handlerCount = 0,
    ) {
    }

    /**
     * Get the event type that was dispatched
     *
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * Get total number of handlers that were executed
     *
     * @return int
     */
    public function getHandlerCount(): int
    {
        return $this->handlerCount;
    }

    /**
     * Record a successful handler execution
     *
     * @param mixed $handler The handler that executed successfully
     * @param mixed $returnValue Optional return value from handler
     * @return self
     */
    public function recordSuccess(mixed $handler, mixed $returnValue = null): self
    {
        $this->successes[] = [
            'handler' => $handler,
            'return_value' => $returnValue,
        ];

        return $this;
    }

    /**
     * Record a failed handler execution
     *
     * @param mixed $handler The handler that failed
     * @param \Throwable $error The error that occurred
     * @return self
     */
    public function recordFailure(mixed $handler, \Throwable $error): self
    {
        $this->failures[] = [
            'handler' => $handler,
            'error' => $error,
        ];

        return $this;
    }

    /**
     * Get all successful handler executions
     *
     * @return array<int, array{handler: mixed, return_value: mixed}>
     */
    public function getSuccesses(): array
    {
        return $this->successes;
    }

    /**
     * Get all failed handler executions
     *
     * @return array<int, array{handler: mixed, error: \Throwable}>
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * Check if any handlers failed
     *
     * @return bool
     */
    public function hasFailures(): bool
    {
        return !empty($this->failures);
    }

    /**
     * Get count of successful handler executions
     *
     * @return int
     */
    public function getSuccessCount(): int
    {
        return count($this->successes);
    }

    /**
     * Get count of failed handler executions
     *
     * @return int
     */
    public function getFailureCount(): int
    {
        return count($this->failures);
    }

    /**
     * Check if all handlers succeeded
     *
     * @return bool
     */
    public function allSucceeded(): bool
    {
        return $this->getFailureCount() === 0 && $this->getSuccessCount() > 0;
    }

    /**
     * Convert result to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'event_type' => $this->eventType,
            'handler_count' => $this->handlerCount,
            'success_count' => $this->getSuccessCount(),
            'failure_count' => $this->getFailureCount(),
            'all_succeeded' => $this->allSucceeded(),
            'failures' => array_map(function ($failure) {
                return [
                    'handler' => $this->serializeHandler($failure['handler']),
                    'error' => [
                        'message' => $failure['error']->getMessage(),
                        'code' => $failure['error']->getCode(),
                        'file' => $failure['error']->getFile(),
                        'line' => $failure['error']->getLine(),
                    ],
                ];
            }, $this->failures),
        ];
    }

    /**
     * Serialize handler information for logging/debugging
     *
     * @param mixed $handler
     * @return string
     */
    private function serializeHandler(mixed $handler): string
    {
        if ($handler instanceof \Closure) {
            return 'Closure';
        } elseif (is_object($handler)) {
            return get_class($handler);
        } elseif (is_array($handler) && count($handler) === 2) {
            $class = is_object($handler[0]) ? get_class($handler[0]) : $handler[0];
            return "{$class}::{$handler[1]}";
        } elseif (is_string($handler)) {
            return $handler;
        }

        return gettype($handler);
    }
}
