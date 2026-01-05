<?php

namespace LemonSqueezy\Http;

use LemonSqueezy\Exception\RateLimitException;
use DateTime;

/**
 * Rate limiter for LemonSqueezy API (300 requests per minute)
 */
class RateLimiter
{
    private const LIMIT_PER_MINUTE = 300;
    private array $requests = [];
    private ?DateTime $resetTime = null;

    /**
     * Record a request and check if rate limit is exceeded
     *
     * @throws RateLimitException If rate limit is exceeded
     */
    public function recordRequest(): void
    {
        $now = time();
        $oneMinuteAgo = $now - 60;

        // Remove requests older than 1 minute
        $this->requests = array_filter(
            $this->requests,
            fn($timestamp) => $timestamp > $oneMinuteAgo
        );

        // Check if we've hit the limit
        if (count($this->requests) >= self::LIMIT_PER_MINUTE) {
            $this->resetTime = new DateTime('@' . (max($this->requests) + 60));
            $remaining = 0;

            throw new RateLimitException(
                'Rate limit exceeded: 300 requests per minute',
                429,
                $this->resetTime,
                $remaining
            );
        }

        // Record this request
        $this->requests[$now . '.' . uniqid()] = $now;
    }

    /**
     * Get remaining requests in current minute
     */
    public function getRemainingRequests(): int
    {
        $now = time();
        $oneMinuteAgo = $now - 60;

        $recentRequests = array_filter(
            $this->requests,
            fn($timestamp) => $timestamp > $oneMinuteAgo
        );

        return max(0, self::LIMIT_PER_MINUTE - count($recentRequests));
    }

    /**
     * Reset the rate limiter
     */
    public function reset(): void
    {
        $this->requests = [];
        $this->resetTime = null;
    }

    /**
     * Get the next reset time
     */
    public function getResetTime(): ?DateTime
    {
        return $this->resetTime;
    }
}
