<?php

declare(strict_types=1);

namespace LemonSqueezy\Logger;

/**
 * Null logger that discards all log messages
 *
 * Implements LemonSqueezy LoggerInterface for use in the framework
 * when no logging is desired.
 */
class NullLogger implements LoggerInterface
{
    /**
     * Log a message at any level
     *
     * This is a no-op logger that discards all messages.
     */
    public function log($level, $message, array $context = []): void
    {
        // Intentionally empty - null logger discards all messages
    }

    /**
     * Log an emergency message
     */
    public function emergency($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log an alert message
     */
    public function alert($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log a critical message
     */
    public function critical($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log an error message
     */
    public function error($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log a warning message
     */
    public function warning($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log a notice message
     */
    public function notice($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log an info message
     */
    public function info($message, array $context = []): void
    {
        // No-op
    }

    /**
     * Log a debug message
     */
    public function debug($message, array $context = []): void
    {
        // No-op
    }
}
