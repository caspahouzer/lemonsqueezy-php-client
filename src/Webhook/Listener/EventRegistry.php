<?php

namespace LemonSqueezy\Webhook\Listener;

/**
 * Registry for managing event listener registrations
 *
 * Maintains a mapping of event types to their registered listeners.
 * Provides methods for registering and retrieving listeners.
 */
class EventRegistry
{
    /**
     * @var array<string, ListenerCollection>
     */
    private array $listeners = [];

    /**
     * Register a listener for a specific event type
     *
     * @param string $eventType The event type (e.g., 'order.created')
     * @param callable|EventListenerInterface $listener The listener/handler
     * @return self
     */
    public function register(string $eventType, callable|EventListenerInterface $listener): self
    {
        if (!isset($this->listeners[$eventType])) {
            $this->listeners[$eventType] = new ListenerCollection();
        }

        $this->listeners[$eventType]->add($listener);

        return $this;
    }

    /**
     * Get all listeners for a specific event type
     *
     * @param string $eventType The event type to look up
     * @return ListenerCollection
     */
    public function getListeners(string $eventType): ListenerCollection
    {
        return $this->listeners[$eventType] ?? new ListenerCollection();
    }

    /**
     * Check if there are any listeners for an event type
     *
     * @param string $eventType The event type to check
     * @return bool
     */
    public function hasListeners(string $eventType): bool
    {
        return isset($this->listeners[$eventType]) && !$this->listeners[$eventType]->isEmpty();
    }

    /**
     * Get all registered event types
     *
     * @return array<string>
     */
    public function getEventTypes(): array
    {
        return array_keys($this->listeners);
    }

    /**
     * Get all listener registrations
     *
     * @return array<string, ListenerCollection>
     */
    public function all(): array
    {
        return $this->listeners;
    }

    /**
     * Clear all listeners (dangerous - use with caution)
     *
     * @return self
     */
    public function clear(): self
    {
        $this->listeners = [];
        return $this;
    }

    /**
     * Clear listeners for a specific event type
     *
     * @param string $eventType The event type to clear
     * @return self
     */
    public function clearEvent(string $eventType): self
    {
        unset($this->listeners[$eventType]);
        return $this;
    }

    /**
     * Get listener count for a specific event type
     *
     * @param string $eventType The event type to count
     * @return int
     */
    public function countListeners(string $eventType): int
    {
        return $this->getListeners($eventType)->count();
    }

    /**
     * Get total count of all listeners across all events
     *
     * @return int
     */
    public function countAll(): int
    {
        $total = 0;
        foreach ($this->listeners as $collection) {
            $total += $collection->count();
        }
        return $total;
    }
}
