<?php

namespace LemonSqueezy\Webhook\Listener;

/**
 * Type-safe collection for storing event listeners
 *
 * Provides a structured way to store listeners for a specific event type,
 * ensuring type safety and offering convenient iteration methods.
 */
class ListenerCollection implements \IteratorAggregate
{
    /**
     * @var array<callable|EventListenerInterface>
     */
    private array $listeners = [];

    /**
     * Add a listener to this collection
     *
     * @param callable|EventListenerInterface $listener The listener to add
     * @return self
     */
    public function add(callable|EventListenerInterface $listener): self
    {
        $this->listeners[] = $listener;
        return $this;
    }

    /**
     * Get all listeners in this collection
     *
     * @return array<callable|EventListenerInterface>
     */
    public function all(): array
    {
        return $this->listeners;
    }

    /**
     * Check if this collection has any listeners
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->listeners);
    }

    /**
     * Get the count of listeners in this collection
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->listeners);
    }

    /**
     * Clear all listeners from this collection
     *
     * @return self
     */
    public function clear(): self
    {
        $this->listeners = [];
        return $this;
    }

    /**
     * Iterate over listeners
     *
     * @return \Iterator<callable|EventListenerInterface>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->listeners);
    }
}
