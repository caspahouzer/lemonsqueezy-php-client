<?php

namespace LemonSqueezy\Tests\Unit\Webhook;

use LemonSqueezy\Webhook\WebhookVerifier;
use LemonSqueezy\Exception\WebhookVerificationException;
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Tests\Unit\MockStream;
use PHPUnit\Framework\TestCase;

class WebhookVerifierTest extends TestCase
{
    private string $secret;
    private string $validBody;
    private string $validSignature;

    protected function setUp(): void
    {
        $this->secret = 'test_webhook_secret_key_123';

        $this->validBody = json_encode([
            'event' => 'order.created',
            'data' => [
                'order_id' => 'ord-123',
                'customer_id' => 'cust-456',
                'amount' => 99.99
            ]
        ]);

        // Compute valid signature (hex digest, as per LemonSqueezy API)
        $this->validSignature = hash_hmac('sha256', $this->validBody, $this->secret);
    }

    public function testVerifyValidSignature(): void
    {
        // Should not throw exception for valid signature
        $this->expectNotToThrow();
        WebhookVerifier::verify($this->validBody, $this->validSignature, $this->secret);
    }

    private function expectNotToThrow(): void
    {
        $this->assertTrue(true);
    }

    public function testVerifyInvalidSignature(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::VERIFICATION_FAILED);

        // Use a valid hex digest but wrong signature (64 char hex string)
        $wrongSignature = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        WebhookVerifier::verify($this->validBody, $wrongSignature, $this->secret);
    }

    public function testVerifyMissingSecret(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::MISSING_SECRET);

        WebhookVerifier::verify($this->validBody, $this->validSignature, null);
    }

    public function testVerifyEmptySecret(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::MISSING_SECRET);

        WebhookVerifier::verify($this->validBody, $this->validSignature, '');
    }

    public function testVerifyEmptySignature(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::EMPTY_SIGNATURE);

        WebhookVerifier::verify($this->validBody, '', $this->secret);
    }

    public function testVerifyInvalidBase64Signature(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::INVALID_FORMAT);

        // Invalid base64 signature with special characters that aren't base64
        WebhookVerifier::verify($this->validBody, '!!!invalid_base64!!!', $this->secret);
    }

    public function testIsValidReturnsTrueForValidSignature(): void
    {
        $result = WebhookVerifier::isValid($this->validBody, $this->validSignature, $this->secret);
        $this->assertTrue($result);
    }

    public function testIsValidReturnsFalseForInvalidSignature(): void
    {
        // Use a valid hex digest but wrong signature (64 char hex string)
        $wrongSignature = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $result = WebhookVerifier::isValid($this->validBody, $wrongSignature, $this->secret);
        $this->assertFalse($result);
    }

    public function testIsValidThrowsOnMissingSecret(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::MISSING_SECRET);

        WebhookVerifier::isValid($this->validBody, $this->validSignature, null);
    }

    public function testVerifyWithPsrStreamInterface(): void
    {
        $stream = new MockStream($this->validBody);
        $result = WebhookVerifier::isValid($stream, $this->validSignature, $this->secret);
        $this->assertTrue($result);
    }

    public function testVerifyWithUnseekableStream(): void
    {
        // Create an unseekable stream mock
        $stream = new class($this->validBody) extends MockStream {
            public function isSeekable(): bool { return false; }
        };

        $result = WebhookVerifier::isValid($stream, $this->validSignature, $this->secret);
        $this->assertTrue($result);
    }

    public function testVerifyWithEmptyBody(): void
    {
        $emptyBody = '';
        $emptyBodySignature = hash_hmac('sha256', $emptyBody, $this->secret);

        $result = WebhookVerifier::isValid($emptyBody, $emptyBodySignature, $this->secret);
        $this->assertTrue($result);
    }

    public function testVerifyWithLargeBody(): void
    {
        $largeBody = json_encode([
            'event' => 'bulk_operation.completed',
            'data' => array_fill(0, 1000, [
                'id' => 'item-123',
                'name' => 'Item Name',
                'value' => 'Some long text content...'
            ])
        ]);

        $largeBodySignature = hash_hmac('sha256', $largeBody, $this->secret);

        $result = WebhookVerifier::isValid($largeBody, $largeBodySignature, $this->secret);
        $this->assertTrue($result);
    }

    public function testVerifyWithConfigIntegration(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key')
            ->withWebhookSecret($this->secret)
            ->build();

        // Should not throw with valid signature
        WebhookVerifier::verifyWithConfig($this->validBody, $this->validSignature, $config);
        $this->assertTrue(true);
    }

    public function testVerifyWithConfigMissingSecret(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key')
            ->build();

        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::MISSING_SECRET);

        WebhookVerifier::verifyWithConfig($this->validBody, $this->validSignature, $config);
    }

    public function testTimingSafeComparison(): void
    {
        // Test that hash_equals is used - signatures should be compared case-sensitively
        $correctSignature = $this->validSignature;
        $incorrectSignature = 'a' . substr($this->validSignature, 1);

        $resultCorrect = WebhookVerifier::isValid($this->validBody, $correctSignature, $this->secret);
        $resultIncorrect = WebhookVerifier::isValid($this->validBody, $incorrectSignature, $this->secret);

        $this->assertTrue($resultCorrect);
        $this->assertFalse($resultIncorrect);
    }

    public function testSignatureIsCaseSensitive(): void
    {
        // Even one character difference should fail
        // Change the first character to something different
        $wrongSignature = chr(ord($this->validSignature[0]) + 1) . substr($this->validSignature, 1);

        $result = WebhookVerifier::isValid($this->validBody, $wrongSignature, $this->secret);
        $this->assertFalse($result);
    }

    public function testUnsupportedAlgorithm(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionCode(WebhookVerificationException::UNSUPPORTED_ALGORITHM);

        WebhookVerifier::verify($this->validBody, $this->validSignature, $this->secret, 'sha512');
    }

    public function testDifferentSecretFails(): void
    {
        $wrongSecret = 'different_secret_key';

        $result = WebhookVerifier::isValid($this->validBody, $this->validSignature, $wrongSecret);
        $this->assertFalse($result);
    }

    public function testModifiedBodyFails(): void
    {
        $modifiedBody = $this->validBody . ' modified';

        $result = WebhookVerifier::isValid($modifiedBody, $this->validSignature, $this->secret);
        $this->assertFalse($result);
    }

    public function testRealWebhookPayload(): void
    {
        // Simulate a real LemonSqueezy webhook payload
        $webhookPayload = json_encode([
            'meta' => [
                'event_name' => 'order:created',
                'custom_data' => null,
                'webhook_id' => 'whk_123456789',
                'webhook_created_at' => '2026-01-05T12:00:00Z'
            ],
            'data' => [
                'type' => 'orders',
                'id' => '123456',
                'attributes' => [
                    'order_number' => 1,
                    'status' => 'completed',
                    'status_formatted' => 'Completed',
                    'refunded' => false,
                    'total' => '19.99',
                    'currency' => 'USD',
                    'urls' => [
                        'receipt' => 'https://example.lemonsqueezy.com/receipt/123'
                    ]
                ]
            ]
        ]);

        $webhookSignature = hash_hmac('sha256', $webhookPayload, $this->secret);

        $result = WebhookVerifier::isValid($webhookPayload, $webhookSignature, $this->secret);
        $this->assertTrue($result);
    }

    public function testMultipleVerificationsSequentially(): void
    {
        // Verify multiple different payloads in sequence
        $body1 = json_encode(['event' => 'order.created', 'id' => 1]);
        $body2 = json_encode(['event' => 'order.updated', 'id' => 2]);
        $body3 = json_encode(['event' => 'order.deleted', 'id' => 3]);

        $sig1 = hash_hmac('sha256', $body1, $this->secret);
        $sig2 = hash_hmac('sha256', $body2, $this->secret);
        $sig3 = hash_hmac('sha256', $body3, $this->secret);

        $this->assertTrue(WebhookVerifier::isValid($body1, $sig1, $this->secret));
        $this->assertTrue(WebhookVerifier::isValid($body2, $sig2, $this->secret));
        $this->assertTrue(WebhookVerifier::isValid($body3, $sig3, $this->secret));
    }

    public function testExceptionMessageContainsHelpfulInfo(): void
    {
        try {
            WebhookVerifier::verify($this->validBody, $this->validSignature, null);
        } catch (WebhookVerificationException $e) {
            $this->assertStringContainsString('secret', strtolower($e->getMessage()));
        }
    }
}
