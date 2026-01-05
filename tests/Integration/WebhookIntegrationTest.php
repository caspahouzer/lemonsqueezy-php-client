<?php

namespace LemonSqueezy\Tests\Integration;

use LemonSqueezy\Webhook\WebhookVerifier;
use LemonSqueezy\Exception\WebhookVerificationException;
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Client;
use LemonSqueezy\ClientFactory;
use LemonSqueezy\Tests\Unit\MockHttpClient;
use PHPUnit\Framework\TestCase;

class WebhookIntegrationTest extends TestCase
{
    private string $webhookSecret;
    private string $testPayload;
    private string $testSignature;
    private ConfigBuilder $configBuilder;

    protected function setUp(): void
    {
        $this->webhookSecret = 'test_webhook_secret_key_integration';

        $this->testPayload = json_encode([
            'meta' => [
                'event_name' => 'order:created',
                'webhook_id' => 'whk_test123',
                'webhook_created_at' => '2026-01-05T12:00:00Z'
            ],
            'data' => [
                'type' => 'orders',
                'id' => '654321',
                'attributes' => [
                    'order_number' => 42,
                    'status' => 'completed',
                    'total' => '99.99',
                    'currency' => 'USD'
                ]
            ]
        ]);

        // Pre-compute the valid signature for this payload (hex digest)
        $this->testSignature = hash_hmac('sha256', $this->testPayload, $this->webhookSecret);

        $this->configBuilder = new ConfigBuilder();
        $this->configBuilder->withApiKey('test_integration_key');
        $this->configBuilder->withHttpClient(new MockHttpClient());
    }

    public function testVerifyWithConfigIntegration(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        // Should not throw exception
        WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);
        $this->assertTrue(true);
    }

    public function testVerifyWithConfigThrowsWhenSecretNotSet(): void
    {
        $config = $this->configBuilder->build();

        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::MISSING_SECRET);

        WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);
    }

    public function testClientVerifyWebhookSignatureMethod(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        $client = new Client($config);

        // Should not throw exception
        $client->verifyWebhookSignature($this->testPayload, $this->testSignature);
        $this->assertTrue(true);
    }

    public function testClientVerifyWebhookSignatureThrowsOnInvalidSignature(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        $client = new Client($config);

        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::VERIFICATION_FAILED);

        // Use a valid hex digest but wrong signature (64 char hex string)
        $wrongSignature = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $client->verifyWebhookSignature($this->testPayload, $wrongSignature);
    }

    public function testClientVerifyWebhookSignatureThrowsWhenSecretNotConfigured(): void
    {
        $config = $this->configBuilder->build();
        $client = new Client($config);

        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::MISSING_SECRET);

        $client->verifyWebhookSignature($this->testPayload, $this->testSignature);
    }

    public function testMultipleWebhooksWithDifferentPayloads(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        $client = new Client($config);

        // Test multiple different webhooks
        $webhooks = [
            [
                'event' => 'order:created',
                'data' => ['order_id' => 'ord-1', 'amount' => 10.00]
            ],
            [
                'event' => 'order:updated',
                'data' => ['order_id' => 'ord-2', 'amount' => 20.00]
            ],
            [
                'event' => 'order:refunded',
                'data' => ['order_id' => 'ord-3', 'amount' => 30.00]
            ],
        ];

        foreach ($webhooks as $webhook) {
            $payload = json_encode($webhook);
            $signature = hash_hmac('sha256', $payload, $this->webhookSecret);

            // All should verify successfully
            $client->verifyWebhookSignature($payload, $signature);
        }

        $this->assertTrue(true);
    }

    public function testWebhookVerificationWithConfigFactory(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_factory_key')
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);
        $this->assertTrue(true);
    }

    public function testWebhookVerificationSequentialWithStatefulConfig(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        // Verify the same payload multiple times
        for ($i = 0; $i < 5; $i++) {
            WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);
        }

        $this->assertTrue(true);
    }

    public function testWebhookVerificationWithRealWorldPayload(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        // Simulate a real order webhook from LemonSqueezy
        $realPayload = json_encode([
            'meta' => [
                'event_name' => 'order:created',
                'custom_data' => null,
                'webhook_id' => 'whk_3f4d5e6c7b8a9f0d',
                'webhook_created_at' => '2026-01-05T15:30:45Z'
            ],
            'data' => [
                'type' => 'orders',
                'id' => '1234567',
                'attributes' => [
                    'store_id' => 123456,
                    'customer_id' => 789012,
                    'identifier' => 'order-789012',
                    'order_number' => 1001,
                    'user_name' => 'John Doe',
                    'user_email' => 'john@example.com',
                    'currency' => 'USD',
                    'currency_rate' => '1.00',
                    'subtotal' => '9999',
                    'discount_total' => '0',
                    'tax' => '0',
                    'total' => '9999',
                    'total_usd' => '9999',
                    'tax_name' => null,
                    'tax_rate' => null,
                    'status' => 'completed',
                    'status_formatted' => 'Completed',
                    'refunded' => false,
                    'refunded_at' => null,
                    'created_at' => '2026-01-05T15:30:00Z',
                    'urls' => [
                        'receipt' => 'https://example.lemonsqueezy.com/receipt/1234567'
                    ],
                    'first_order_item' => [
                        'id' => '9876543',
                        'order_id' => '1234567',
                        'product_id' => '111111',
                        'variant_id' => '222222',
                        'product_name' => 'Test Product',
                        'variant_name' => 'Test Variant',
                        'license_key' => 'LICENSE-KEY-EXAMPLE',
                        'quantity' => 1,
                        'created_at' => '2026-01-05T15:30:00Z'
                    ]
                ]
            ]
        ]);

        $signature = hash_hmac('sha256', $realPayload, $this->webhookSecret);

        WebhookVerifier::verifyWithConfig($realPayload, $signature, $config);
        $this->assertTrue(true);
    }

    public function testExceptionPropagatesFromVerifierToClient(): void
    {
        $config = $this->configBuilder->build(); // No secret configured
        $client = new Client($config);

        try {
            $client->verifyWebhookSignature($this->testPayload, $this->testSignature);
            $this->fail('Expected WebhookVerificationException');
        } catch (WebhookVerificationException $e) {
            // Verify exception comes from WebhookVerifier (MISSING_SECRET code)
            $this->assertEquals(WebhookVerificationException::MISSING_SECRET, $e->getCode());
            $this->assertStringContainsString('secret', strtolower($e->getMessage()));
        }
    }

    public function testVerificationWithDifferentSecretFails(): void
    {
        $wrongSecret = 'different_secret_key';

        $config = $this->configBuilder
            ->withWebhookSecret($wrongSecret)
            ->build();

        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::VERIFICATION_FAILED);

        WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);
    }

    public function testClientMethodWorksWithFactory(): void
    {
        $client = ClientFactory::create('test_factory_api_key');

        // This should throw because no secret is configured
        $this->expectException(WebhookVerificationException::class);
        $client->verifyWebhookSignature($this->testPayload, $this->testSignature);
    }

    public function testConfigurationPersistenceAcrossVerifications(): void
    {
        $config = $this->configBuilder
            ->withWebhookSecret($this->webhookSecret)
            ->build();

        // Verify that config doesn't change between calls
        $this->assertEquals($this->webhookSecret, $config->getWebhookSecret());

        WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);

        // Secret should still be the same
        $this->assertEquals($this->webhookSecret, $config->getWebhookSecret());

        // Verification should still work
        WebhookVerifier::verifyWithConfig($this->testPayload, $this->testSignature, $config);
    }
}
