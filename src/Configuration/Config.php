<?php

namespace LemonSqueezy\Configuration;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Immutable configuration holder for the LemonSqueezy API client
 */
class Config
{
    private const API_BASE_URL = 'https://api.lemonsqueezy.com/v1';
    private const REQUEST_TIMEOUT = 30;
    private const MAX_RETRIES = 3;

    /**
     * @param Credentials $credentials API credentials
     * @param ?ClientInterface $httpClient PSR-18 HTTP client
     * @param ?RequestFactoryInterface $requestFactory PSR-17 request factory
     * @param ?StreamFactoryInterface $streamFactory PSR-17 stream factory
     * @param int $timeout Request timeout in seconds
     * @param int $maxRetries Maximum number of retries for failed requests
     * @param ?string $webhookSecret Secret for webhook signature verification
     */
    public function __construct(
        private Credentials $credentials,
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $requestFactory = null,
        private ?StreamFactoryInterface $streamFactory = null,
        private int $timeout = self::REQUEST_TIMEOUT,
        private int $maxRetries = self::MAX_RETRIES,
        private ?string $webhookSecret = null,
    ) {}

    /**
     * Get API credentials
     */
    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    /**
     * Get PSR-18 HTTP client
     */
    public function getHttpClient(): ?ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Get PSR-17 request factory
     */
    public function getRequestFactory(): ?RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    /**
     * Get PSR-17 stream factory
     */
    public function getStreamFactory(): ?StreamFactoryInterface
    {
        return $this->streamFactory;
    }

    /**
     * Get request timeout in seconds
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get maximum number of retries
     */
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * Get webhook secret for signature verification
     */
    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    /**
     * Get API base URL
     */
    public function getApiBaseUrl(): string
    {
        return self::API_BASE_URL;
    }

    /**
     * Check if authenticated (API key is set)
     */
    public function isAuthenticated(): bool
    {
        return $this->credentials->hasApiKey();
    }
}
