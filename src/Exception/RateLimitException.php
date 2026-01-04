<?php

namespace LemonSqueezy\Exception;

use DateTimeInterface;

/**
 * Exception for rate limit exceeded (HTTP 429)
 */
class RateLimitException extends ClientException
{
    protected ?DateTimeInterface $resetTime = null;
    protected int $remainingRequests = 0;

    /**
     * @param string $message
     * @param int $code
     * @param ?DateTimeInterface $resetTime When the rate limit will reset
     * @param int $remainingRequests Remaining requests until reset
     * @param ?array $response The API response data
     * @param ?\Throwable $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?DateTimeInterface $resetTime = null,
        int $remainingRequests = 0,
        ?array $response = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $response, $previous);
        $this->resetTime = $resetTime;
        $this->remainingRequests = $remainingRequests;
    }

    /**
     * Get the time when the rate limit will reset
     */
    public function getResetTime(): ?DateTimeInterface
    {
        return $this->resetTime;
    }

    /**
     * Get remaining requests before hitting limit
     */
    public function getRemainingRequests(): int
    {
        return $this->remainingRequests;
    }

    /**
     * Get seconds to wait before retrying
     */
    public function getSecondsUntilReset(): int
    {
        if ($this->resetTime === null) {
            return 0;
        }

        $now = new \DateTime();
        $diff = $this->resetTime->getTimestamp() - $now->getTimestamp();

        return max(0, $diff);
    }
}
