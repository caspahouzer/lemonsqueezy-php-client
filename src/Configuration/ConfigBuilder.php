<?php

namespace LemonSqueezy\Configuration;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Factory\RequestFactoryInterface;
use Psr\Http\Factory\StreamFactoryInterface;

/**
 * Fluent builder for creating immutable Config instances
 */
class ConfigBuilder
{
    private ?string $apiKey = null;
    private ?ClientInterface $httpClient = null;
    private ?RequestFactoryInterface $requestFactory = null;
    private ?StreamFactoryInterface $streamFactory = null;
    private int $timeout = 30;
    private int $maxRetries = 3;
    private ?string $webhookSecret = null;

    /**
     * Set the API key for Bearer token authentication
     */
    public function withApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Set the PSR-18 HTTP client
     */
    public function withHttpClient(ClientInterface $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Set the PSR-17 request factory
     */
    public function withRequestFactory(RequestFactoryInterface $factory): self
    {
        $this->requestFactory = $factory;

        return $this;
    }

    /**
     * Set the PSR-17 stream factory
     */
    public function withStreamFactory(StreamFactoryInterface $factory): self
    {
        $this->streamFactory = $factory;

        return $this;
    }

    /**
     * Set request timeout in seconds
     */
    public function withTimeout(int $seconds): self
    {
        $this->timeout = max(1, $seconds);

        return $this;
    }

    /**
     * Set maximum number of retries
     */
    public function withMaxRetries(int $retries): self
    {
        $this->maxRetries = max(0, $retries);

        return $this;
    }

    /**
     * Set webhook secret for signature verification
     */
    public function withWebhookSecret(string $secret): self
    {
        $this->webhookSecret = $secret;

        return $this;
    }

    /**
     * Build the immutable Config instance
     */
    public function build(): Config
    {
        $credentials = new Credentials($this->apiKey);

        return new Config(
            $credentials,
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            $this->timeout,
            $this->maxRetries,
            $this->webhookSecret,
        );
    }
}
