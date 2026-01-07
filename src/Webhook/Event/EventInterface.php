<?php

namespace LemonSqueezy\Webhook\Event;

/**
 * Contract for webhook events dispatched by the EventDispatcher
 *
 * Implementations should wrap webhook payloads with metadata about
 * the event, including type, timestamp, and verification status.
 */
interface EventInterface
{
    /**
     * Get the event type identifier (e.g., 'order.created', 'subscription.updated')
     *
     * @return string
     */
    public function getEventType(): string;

    /**
     * Get the raw webhook payload as an array
     *
     * @return array
     */
    public function getPayload(): array;

    /**
     * Get event metadata containing timing and verification information
     *
     * @return EventMetadata
     */
    public function getMetadata(): EventMetadata;

    /**
     * Get deserialized data from the webhook payload
     *
     * For LemonSqueezy webhooks, this returns the main data object
     * (Order, Subscription, etc.) as a model instance.
     *
     * @return mixed The deserialized entity or payload
     */
    public function getData(): mixed;

    /**
     * Mark the webhook event as verified (signature check passed)
     *
     * @return self For method chaining
     */
    public function markVerified(): self;

    /**
     * Check if this webhook event was verified
     *
     * @return bool
     */
    public function isVerified(): bool;
}
