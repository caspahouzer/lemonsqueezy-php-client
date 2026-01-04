<?php

namespace LemonSqueezy;

use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Configuration\Config;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Factory\RequestFactoryInterface;
use Psr\Http\Factory\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Factory for creating and configuring LemonSqueezy API clients
 *
 * Example usage:
 * ```php
 * $client = ClientFactory::create('lsq_live_...');
 *
 * // Or with custom configuration:
 * $client = (new ClientFactory())
 *     ->withApiKey('lsq_live_...')
 *     ->withTimeout(60)
 *     ->withLogger($logger)
 *     ->build();
 * ```
 */
class ClientFactory
{
    private ConfigBuilder $configBuilder;

    public function __construct()
    {
        $this->configBuilder = new ConfigBuilder();
    }

    /**
     * Create a client with just an API key
     */
    public static function create(string $apiKey, ?LoggerInterface $logger = null): Client
    {
        return (new self())
            ->withApiKey($apiKey)
            ->build($logger);
    }

    /**
     * Set the API key
     */
    public function withApiKey(string $apiKey): self
    {
        $this->configBuilder->withApiKey($apiKey);

        return $this;
    }

    /**
     * Set the HTTP client
     */
    public function withHttpClient(ClientInterface $client): self
    {
        $this->configBuilder->withHttpClient($client);

        return $this;
    }

    /**
     * Set the request factory
     */
    public function withRequestFactory(RequestFactoryInterface $factory): self
    {
        $this->configBuilder->withRequestFactory($factory);

        return $this;
    }

    /**
     * Set the stream factory
     */
    public function withStreamFactory(StreamFactoryInterface $factory): self
    {
        $this->configBuilder->withStreamFactory($factory);

        return $this;
    }

    /**
     * Set request timeout
     */
    public function withTimeout(int $seconds): self
    {
        $this->configBuilder->withTimeout($seconds);

        return $this;
    }

    /**
     * Set maximum retries
     */
    public function withMaxRetries(int $retries): self
    {
        $this->configBuilder->withMaxRetries($retries);

        return $this;
    }

    /**
     * Set webhook secret
     */
    public function withWebhookSecret(string $secret): self
    {
        $this->configBuilder->withWebhookSecret($secret);

        return $this;
    }

    /**
     * Build the client
     */
    public function build(?LoggerInterface $logger = null): Client
    {
        $config = $this->configBuilder->build();

        return new Client($config, $logger);
    }
}
