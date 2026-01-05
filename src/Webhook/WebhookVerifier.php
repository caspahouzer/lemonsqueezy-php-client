<?php

namespace LemonSqueezy\Webhook;

use LemonSqueezy\Configuration\Config;
use LemonSqueezy\Exception\WebhookVerificationException;
use Psr\Http\Message\StreamInterface;

/**
 * Utility class for verifying LemonSqueezy webhook signatures
 *
 * Provides three ways to verify webhook signatures:
 * 1. verify() - Throws exception on failure
 * 2. isValid() - Returns boolean
 * 3. verifyWithConfig() - Uses secret from Config object
 *
 * Example usage:
 * ```php
 * $body = file_get_contents('php://input');
 * $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
 *
 * try {
 *     WebhookVerifier::verify($body, $signature, $webhookSecret);
 * } catch (WebhookVerificationException $e) {
 *     http_response_code(401);
 * }
 * ```
 */
class WebhookVerifier
{
    private const ALGORITHM_SHA256 = 'sha256';

    /**
     * Verify webhook signature, throws exception on failure
     *
     * @param string|StreamInterface $body The webhook request body
     * @param string $signature The signature from webhook header
     * @param ?string $secret The webhook secret
     * @param string $algorithm The hash algorithm to use
     * @throws WebhookVerificationException If verification fails
     */
    public static function verify(
        string|StreamInterface $body,
        string $signature,
        ?string $secret = null,
        string $algorithm = self::ALGORITHM_SHA256
    ): void {
        if (!self::isValid($body, $signature, $secret, $algorithm)) {
            throw new WebhookVerificationException(
                'Webhook signature verification failed',
                WebhookVerificationException::VERIFICATION_FAILED
            );
        }
    }

    /**
     * Verify webhook signature, returns boolean
     *
     * @param string|StreamInterface $body The webhook request body
     * @param string $signature The signature from webhook header
     * @param ?string $secret The webhook secret
     * @param string $algorithm The hash algorithm to use
     * @return bool True if signature is valid, false otherwise
     * @throws WebhookVerificationException If configuration is invalid
     */
    public static function isValid(
        string|StreamInterface $body,
        string $signature,
        ?string $secret = null,
        string $algorithm = self::ALGORITHM_SHA256
    ): bool {
        // Validate inputs
        if (empty($secret)) {
            throw new WebhookVerificationException(
                'Webhook secret is required for verification',
                WebhookVerificationException::MISSING_SECRET
            );
        }

        if (empty($signature)) {
            throw new WebhookVerificationException(
                'Webhook signature cannot be empty',
                WebhookVerificationException::EMPTY_SIGNATURE
            );
        }

        // Validate signature format (should be a hex digest)
        if (!self::isValidHexDigest($signature)) {
            throw new WebhookVerificationException(
                'Invalid signature format: expected hex digest value',
                WebhookVerificationException::INVALID_FORMAT
            );
        }

        // Validate algorithm
        if ($algorithm !== self::ALGORITHM_SHA256) {
            throw new WebhookVerificationException(
                sprintf('Unsupported hash algorithm: %s', $algorithm),
                WebhookVerificationException::UNSUPPORTED_ALGORITHM
            );
        }

        // Convert body to string if it's a PSR-7 stream
        $bodyString = self::bodyToString($body);

        // Compute expected signature (HMAC-SHA256 as hex digest)
        $expectedSignature = hash_hmac($algorithm, $bodyString, $secret);

        // Timing-safe comparison
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify webhook signature using secret from Config object
     *
     * @param string|StreamInterface $body The webhook request body
     * @param string $signature The signature from webhook header
     * @param Config $config The LemonSqueezy configuration object
     * @param string $algorithm The hash algorithm to use
     * @throws WebhookVerificationException If verification fails
     */
    public static function verifyWithConfig(
        string|StreamInterface $body,
        string $signature,
        Config $config,
        string $algorithm = self::ALGORITHM_SHA256
    ): void {
        $secret = $config->getWebhookSecret();
        self::verify($body, $signature, $secret, $algorithm);
    }

    /**
     * Check if a string is a valid hex digest
     *
     * @param string $string The string to validate
     * @return bool True if valid hex digest, false otherwise
     */
    private static function isValidHexDigest(string $string): bool
    {
        // Check if string contains only valid hex characters (0-9, a-f, A-F)
        // SHA256 produces a 64-character hex string
        return (bool) preg_match('/^[a-fA-F0-9]{64}$/', $string);
    }

    /**
     * Convert body (string or PSR-7 stream) to string
     *
     * @param string|StreamInterface $body
     * @return string
     */
    private static function bodyToString(string|StreamInterface $body): string
    {
        if (is_string($body)) {
            return $body;
        }

        // Handle PSR-7 StreamInterface
        if ($body instanceof StreamInterface) {
            // Check if stream is seekable before reading
            if ($body->isSeekable()) {
                $body->rewind();
            }

            return $body->getContents();
        }

        return '';
    }
}
