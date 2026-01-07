<?php

namespace LemonSqueezy\Webhook\Listener;

use LemonSqueezy\Webhook\Event\EventInterface;

/**
 * Contract for event listeners/handlers
 *
 * Implementations should handle a specific webhook event type
 * and perform necessary actions (logging, database updates, etc.).
 *
 * Listeners can be registered as:
 * - Closure/callable functions
 * - Classes implementing this interface
 *
 * Example:
 * ```php
 * class OrderCreatedListener implements EventListenerInterface
 * {
 *     public function handle(EventInterface $event): void
 *     {
 *         $order = $event->getData();
 *         // Process order creation
 *     }
 * }
 * ```
 */
interface EventListenerInterface
{
    /**
     * Handle a webhook event
     *
     * @param EventInterface $event The webhook event to handle
     * @throws \Exception Any exception thrown will be caught and reported
     */
    public function handle(EventInterface $event): void;
}
