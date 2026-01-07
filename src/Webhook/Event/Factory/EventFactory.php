<?php

namespace LemonSqueezy\Webhook\Event\Factory;

use LemonSqueezy\Webhook\Event\{EventInterface, WebhookEvent, EventMetadata};
use LemonSqueezy\Exception\LemonSqueezyException;

/**
 * Factory for creating webhook event objects from payloads
 *
 * Provides convenient methods for constructing event instances
 * from various sources (JSON, arrays, etc.).
 */
class EventFactory
{
    /**
     * Create an event from a JSON string
     *
     * @param string $jsonPayload The raw JSON webhook payload
     * @param ?string $eventType Optional event type override
     * @param ?EventMetadata $metadata Optional metadata
     * @return EventInterface
     * @throws LemonSqueezyException If JSON is invalid
     */
    public static function createFromJson(
        string $jsonPayload,
        ?string $eventType = null,
        ?EventMetadata $metadata = null,
    ): EventInterface {
        return new WebhookEvent($jsonPayload, $eventType, $metadata);
    }

    /**
     * Create an event from an array payload
     *
     * @param array $payload The webhook payload as an array
     * @param ?string $eventType Optional event type override
     * @param ?EventMetadata $metadata Optional metadata
     * @return EventInterface
     */
    public static function createFromArray(
        array $payload,
        ?string $eventType = null,
        ?EventMetadata $metadata = null,
    ): EventInterface {
        return WebhookEvent::fromArray($payload, $eventType, $metadata);
    }

    /**
     * Create an event from a JSON file
     *
     * Useful for testing with fixture files.
     *
     * @param string $filePath Path to JSON file containing webhook payload
     * @param ?string $eventType Optional event type override
     * @return EventInterface
     * @throws LemonSqueezyException If file cannot be read or JSON is invalid
     */
    public static function createFromFile(
        string $filePath,
        ?string $eventType = null,
    ): EventInterface {
        return WebhookEvent::fromFile($filePath, $eventType);
    }

    /**
     * Create an event from HTTP request globals
     *
     * Reads the webhook payload from php://input and extracts
     * the signature from request headers.
     *
     * @param ?string $eventType Optional event type override
     * @param array $headers Optional headers array (defaults to $_SERVER)
     * @return EventInterface
     * @throws LemonSqueezyException If payload cannot be read
     */
    public static function createFromRequest(?string $eventType = null, array $headers = []): EventInterface
    {
        $payload = file_get_contents('php://input');

        if ($payload === false) {
            throw new LemonSqueezyException('Failed to read request body');
        }

        return self::createFromJson($payload, $eventType);
    }

    /**
     * Create event with metadata
     *
     * Useful when you need to set verification status or other metadata
     * before event dispatch.
     *
     * @param string $jsonPayload The webhook JSON payload
     * @param ?string $eventType Optional event type override
     * @param bool $isVerified Whether the signature was verified
     * @param string $algorithm The hash algorithm used
     * @return EventInterface
     */
    public static function createWithMetadata(
        string $jsonPayload,
        ?string $eventType = null,
        bool $isVerified = false,
        string $algorithm = 'sha256',
    ): EventInterface {
        $metadata = new EventMetadata(
            new \DateTime(),
            $isVerified,
            $algorithm,
        );

        return new WebhookEvent($jsonPayload, $eventType, $metadata);
    }
}
