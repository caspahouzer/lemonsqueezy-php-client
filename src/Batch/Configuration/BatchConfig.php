<?php

namespace LemonSqueezy\Batch\Configuration;

/**
 * Configuration for batch operations
 *
 * Defines constants and defaults for batch operation execution,
 * including rate limiting and timeout settings.
 */
class BatchConfig
{
    /**
     * Default delay between operations in milliseconds
     *
     * LemonSqueezy API limit: 300 req/min (5 req/sec)
     * 200ms delay = 5 operations/sec, safe margin below rate limit
     */
    public const DEFAULT_DELAY_MS = 200;

    /**
     * Maximum delay between operations in milliseconds
     */
    public const MAX_DELAY_MS = 5000;

    /**
     * Default timeout per operation in seconds
     */
    public const DEFAULT_TIMEOUT = 30;

    /**
     * Maximum timeout per operation in seconds
     */
    public const MAX_TIMEOUT = 300;

    /**
     * Default setting: whether to stop on first error
     */
    public const DEFAULT_STOP_ON_ERROR = false;

    /**
     * Default number of retry attempts for failed operations
     */
    public const DEFAULT_RETRY_ATTEMPTS = 0;

    /**
     * Maximum retry attempts
     */
    public const MAX_RETRY_ATTEMPTS = 5;

    /**
     * Merge provided options with defaults
     *
     * @param array $options User-provided options
     * @return array Merged options with defaults
     */
    public static function mergeWithDefaults(array $options = []): array
    {
        return array_merge(
            [
                'delayMs' => self::DEFAULT_DELAY_MS,
                'timeout' => self::DEFAULT_TIMEOUT,
                'stopOnError' => self::DEFAULT_STOP_ON_ERROR,
                'retryAttempts' => self::DEFAULT_RETRY_ATTEMPTS,
            ],
            $options
        );
    }

    /**
     * Validate configuration options
     *
     * @param array $options Options to validate
     * @throws \InvalidArgumentException If validation fails
     */
    public static function validate(array $options): void
    {
        if (isset($options['delayMs'])) {
            if ($options['delayMs'] < 0 || $options['delayMs'] > self::MAX_DELAY_MS) {
                throw new \InvalidArgumentException(
                    "delayMs must be between 0 and " . self::MAX_DELAY_MS
                );
            }
        }

        if (isset($options['timeout'])) {
            if ($options['timeout'] <= 0 || $options['timeout'] > self::MAX_TIMEOUT) {
                throw new \InvalidArgumentException(
                    "timeout must be between 1 and " . self::MAX_TIMEOUT
                );
            }
        }

        if (isset($options['retryAttempts'])) {
            if ($options['retryAttempts'] < 0 || $options['retryAttempts'] > self::MAX_RETRY_ATTEMPTS) {
                throw new \InvalidArgumentException(
                    "retryAttempts must be between 0 and " . self::MAX_RETRY_ATTEMPTS
                );
            }
        }

        if (isset($options['stopOnError'])) {
            if (!is_bool($options['stopOnError'])) {
                throw new \InvalidArgumentException(
                    "stopOnError must be a boolean"
                );
            }
        }
    }
}
