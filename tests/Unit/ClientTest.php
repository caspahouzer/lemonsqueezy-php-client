<?php

namespace LemonSqueezy\Tests\Unit;

use LemonSqueezy\Client;
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\ClientFactory;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_key_123456789')
            ->withHttpClient(new MockHttpClient())
            ->build();

        try {
            $this->client = new Client($config);
        } catch (\Throwable $e) {
            $this->markTestSkipped('Guzzle or PSR factories not available: ' . $e->getMessage());
        }
    }

    public function testClientCanBeInstantiated(): void
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    public function testClientReturnsCorrectResources(): void
    {
        $this->assertNotNull($this->client->users());
        $this->assertNotNull($this->client->customers());
        $this->assertNotNull($this->client->products());
        $this->assertNotNull($this->client->orders());
        $this->assertNotNull($this->client->subscriptions());
        $this->assertNotNull($this->client->discounts());
    }

    public function testClientFactoryCreatesClient(): void
    {
        $client = ClientFactory::create('test_api_key_factory');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testClientReturnsConfiguration(): void
    {
        $config = $this->client->getConfig();
        $this->assertNotNull($config);
        $this->assertTrue($config->isAuthenticated());
    }

    public function testClientReturnsLogger(): void
    {
        $logger = $this->client->getLogger();
        $this->assertNotNull($logger);
    }
}
