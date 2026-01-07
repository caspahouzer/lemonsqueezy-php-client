<?php

namespace LemonSqueezy\Exception;

/**
 * Exception thrown when attempting to dispatch an event with no registered listeners
 *
 * This is informational and may not always be treated as an error.
 */
class UnregisteredEventException extends EventDispatcherException
{
    /**
     * Create a new UnregisteredEventException
     *
     * @param string $eventType The event type with no listeners
     */
    public function __construct(
        private string $eventType,
    ) {
        parent::__construct(
            "No listeners registered for event: {$eventType}",
            self::NO_LISTENERS
        );
    }

    /**
     * Get the event type with no listeners
     *
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }
}
