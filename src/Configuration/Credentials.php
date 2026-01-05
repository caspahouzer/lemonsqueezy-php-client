<?php

namespace LemonSqueezy\Configuration;

/**
 * Immutable API credentials holder
 */
class Credentials
{
    /**
     * @param ?string $apiKey The API key for Bearer token authentication
     */
    public function __construct(private ?string $apiKey = null)
    {
    }

    /**
     * Get the API key
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Check if credentials are set
     */
    public function hasApiKey(): bool
    {
        return $this->apiKey !== null && $this->apiKey !== '';
    }

    /**
     * Get masked API key for logging (shows first and last 4 chars)
     */
    public function getMaskedApiKey(): string
    {
        if (!$this->apiKey || strlen($this->apiKey) < 8) {
            return '****';
        }

        $first = substr($this->apiKey, 0, 4);
        $last = substr($this->apiKey, -4);

        return $first . '...' . $last;
    }
}
