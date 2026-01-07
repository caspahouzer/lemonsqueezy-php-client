<?php

namespace LemonSqueezy\Webhook\Event;

use LemonSqueezy\Exception\LemonSqueezyException;

/**
 * Concrete implementation of EventInterface for LemonSqueezy webhooks
 *
 * Wraps a JSON:API formatted webhook payload with metadata about
 * the event, including type, timestamp, and verification status.
 *
 * Example webhook payload:
 * ```
 * {
 *   "meta": {
 *     "event_name": "order.created",
 *     "webhook_id": "..."
 *   },
 *   "data": {
 *     "type": "orders",
 *     "id": "123",
 *     "attributes": { ... }
 *   }
 * }
 * ```
 */
class WebhookEvent implements EventInterface
{
    private ?array $payload = null;
    private ?string $eventType = null;
    private mixed $deserializedData = null;
    private bool $dataSerialized = false;

    /**
     * Create a new WebhookEvent from a JSON string
     *
     * @param string $jsonPayload The raw JSON webhook payload
     * @param ?string $eventType Optional event type override
     * @param ?EventMetadata $metadata Optional metadata (will be created if not provided)
     * @throws LemonSqueezyException If JSON is invalid
     */
    public function __construct(
        string $jsonPayload,
        ?string $eventType = null,
        private ?EventMetadata $metadata = null,
    ) {
        try {
            $this->payload = json_decode($jsonPayload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new LemonSqueezyException("Invalid webhook JSON payload: {$e->getMessage()}");
        }

        // Extract event type from payload or use provided override
        $this->eventType = $eventType ?? $this->extractEventType();

        // Create default metadata if not provided
        if ($this->metadata === null) {
            $this->metadata = new EventMetadata(
                new \DateTime(),
                false,
                'sha256',
            );
        }
    }

    /**
     * Create a WebhookEvent from an array payload
     *
     * Useful for testing or when payload is already deserialized.
     *
     * @param array $payload The webhook payload as an array
     * @param ?string $eventType Optional event type override
     * @param ?EventMetadata $metadata Optional metadata
     * @return self
     */
    public static function fromArray(
        array $payload,
        ?string $eventType = null,
        ?EventMetadata $metadata = null,
    ): self {
        $json = json_encode($payload);
        return new self($json, $eventType, $metadata);
    }

    /**
     * Create a WebhookEvent from a JSON file (useful for testing)
     *
     * @param string $filePath Path to JSON file
     * @param ?string $eventType Optional event type override
     * @return self
     * @throws LemonSqueezyException If file cannot be read
     */
    public static function fromFile(string $filePath, ?string $eventType = null): self
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new LemonSqueezyException("Cannot read webhook file: {$filePath}");
        }

        $jsonPayload = file_get_contents($filePath);
        if ($jsonPayload === false) {
            throw new LemonSqueezyException("Failed to read webhook file: {$filePath}");
        }

        return new self($jsonPayload, $eventType);
    }

    /**
     * Get the event type identifier
     *
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType ?? 'unknown';
    }

    /**
     * Get the raw webhook payload as an array
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload ?? [];
    }

    /**
     * Get event metadata
     *
     * @return EventMetadata
     */
    public function getMetadata(): EventMetadata
    {
        return $this->metadata;
    }

    /**
     * Get deserialized data from the webhook payload
     *
     * Returns the main data object (Order, Subscription, etc.).
     * Deserialization is lazy and cached.
     *
     * @return mixed The deserialized entity or raw data array
     */
    public function getData(): mixed
    {
        if (!$this->dataSerialized && $this->deserializedData === null) {
            $this->deserializedData = $this->extractData();
            $this->dataSerialized = true;
        }

        return $this->deserializedData;
    }

    /**
     * Get the raw data segment from the JSON:API payload
     *
     * @return ?array The 'data' segment from the webhook payload
     */
    public function getRawData(): ?array
    {
        return $this->payload['data'] ?? null;
    }

    /**
     * Get included resources from the webhook payload
     *
     * @return array The 'included' segment from the webhook payload
     */
    public function getIncluded(): array
    {
        return $this->payload['included'] ?? [];
    }

    /**
     * Get webhook metadata from the payload
     *
     * @return array The 'meta' segment from the webhook payload
     */
    public function getWebhookMeta(): array
    {
        return $this->payload['meta'] ?? [];
    }

    /**
     * Mark webhook as verified (signature check passed)
     *
     * @return self
     */
    public function markVerified(): self
    {
        $this->metadata->markVerified();
        return $this;
    }

    /**
     * Check if this webhook was verified
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->metadata->isVerified();
    }

    /**
     * Extract event type from the webhook payload
     *
     * LemonSqueezy includes the event name in meta.event_name
     *
     * @return string
     */
    private function extractEventType(): string
    {
        return $this->payload['meta']['event_name'] ?? 'unknown';
    }

    /**
     * Extract and deserialize the main data from the payload
     *
     * For now, returns the raw data array. This can be enhanced
     * later to deserialize to actual Model entities.
     *
     * @return mixed
     */
    private function extractData(): mixed
    {
        $rawData = $this->getRawData();

        if ($rawData === null) {
            return null;
        }

        // Return the raw data for now
        // Future: Could deserialize to Model entities based on 'type' field
        return $rawData;
    }
}
