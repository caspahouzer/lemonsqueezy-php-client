<?php

namespace LemonSqueezy\Webhook\Dispatcher;

use LemonSqueezy\Webhook\Event\EventInterface;
use LemonSqueezy\Webhook\Listener\EventListenerInterface;
use LemonSqueezy\Webhook\Listener\EventRegistry;

/**
 * Central event dispatcher for webhook events
 *
 * Manages listener registration and event dispatching. Supports both
 * static methods for convenient registration and instance methods
 * for dispatch operations.
 *
 * Example:
 * ```php
 * // Register listeners
 * EventDispatcher::register('order.created', function($event) {
 *     $order = $event->getData();
 *     // Process order creation
 * });
 *
 * // Dispatch an event
 * $dispatcher = new EventDispatcher();
 * $result = $dispatcher->dispatch($event);
 * ```
 */
class EventDispatcher
{
    /**
     * Global listener registry (for static method usage)
     *
     * @var ?EventRegistry
     */
    private static ?EventRegistry $globalRegistry = null;

    /**
     * Instance listener registry
     *
     * @var EventRegistry
     */
    private EventRegistry $registry;

    /**
     * Create a new EventDispatcher
     *
     * Can optionally use a shared registry for all instances,
     * or create isolated registries per instance.
     *
     * @param ?EventRegistry $registry Optional registry to use (uses global by default)
     */
    public function __construct(?EventRegistry $registry = null)
    {
        if ($registry !== null) {
            $this->registry = $registry;
        } else {
            self::$globalRegistry ??= new EventRegistry();
            $this->registry = self::$globalRegistry;
        }
    }

    /**
     * Register a listener for a specific event type (static API)
     *
     * Registers with the global listener registry.
     *
     * @param string $eventType The event type to listen for (e.g., 'order.created')
     * @param callable|EventListenerInterface $listener The listener/handler
     * @return void
     *
     * @example
     * EventDispatcher::register('order.created', function($event) {
     *     echo "Order created: " . $event->getData()['id'];
     * });
     */
    public static function register(string $eventType, callable|EventListenerInterface $listener): void
    {
        self::$globalRegistry ??= new EventRegistry();
        self::$globalRegistry->register($eventType, $listener);
    }

    /**
     * Unregister all listeners for an event type (static API)
     *
     * @param string $eventType The event type to clear
     * @return void
     */
    public static function unregister(string $eventType): void
    {
        self::$globalRegistry ??= new EventRegistry();
        self::$globalRegistry->clearEvent($eventType);
    }

    /**
     * Clear all registered listeners (static API, use with caution)
     *
     * @return void
     */
    public static function clearAll(): void
    {
        self::$globalRegistry ??= new EventRegistry();
        self::$globalRegistry->clear();
    }

    /**
     * Get the global registry (for advanced usage)
     *
     * @return EventRegistry
     */
    public static function getGlobalRegistry(): EventRegistry
    {
        self::$globalRegistry ??= new EventRegistry();
        return self::$globalRegistry;
    }

    /**
     * Dispatch a webhook event to registered listeners
     *
     * Executes all listeners registered for the event type,
     * collecting successes and failures.
     *
     * @param EventInterface $event The event to dispatch
     * @return DispatchResult Result containing handler execution data
     *
     * @example
     * $dispatcher = new EventDispatcher();
     * $result = $dispatcher->dispatch($event);
     *
     * if ($result->hasFailures()) {
     *     error_log("Some handlers failed");
     * }
     */
    public function dispatch(EventInterface $event): DispatchResult
    {
        $eventType = $event->getEventType();
        $listeners = $this->registry->getListeners($eventType);
        $result = new DispatchResult($eventType, $listeners->count());

        foreach ($listeners as $listener) {
            try {
                $this->executeListener($listener, $event);
                $result->recordSuccess($listener);
            } catch (\Throwable $e) {
                $result->recordFailure($listener, $e);
            }
        }

        return $result;
    }

    /**
     * Get the registry for this dispatcher
     *
     * @return EventRegistry
     */
    public function getRegistry(): EventRegistry
    {
        return $this->registry;
    }

    /**
     * Check if there are listeners for an event type
     *
     * @param string $eventType The event type to check
     * @return bool
     */
    public function hasListeners(string $eventType): bool
    {
        return $this->registry->hasListeners($eventType);
    }

    /**
     * Execute a single listener
     *
     * Handles both callable and EventListenerInterface implementations.
     *
     * @param callable|EventListenerInterface $listener The listener to execute
     * @param EventInterface $event The event to pass to listener
     * @throws \Throwable Any exception from the listener
     */
    private function executeListener(callable|EventListenerInterface $listener, EventInterface $event): void
    {
        if ($listener instanceof EventListenerInterface) {
            // Listener implements the interface
            $listener->handle($event);
        } else {
            // Listener is a callable (function, closure, etc.)
            $listener($event);
        }
    }
}
