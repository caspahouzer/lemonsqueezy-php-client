<?php

namespace LemonSqueezy\Exception;

/**
 * Exception thrown when an event is invalid
 *
 * Raised when an event object doesn't meet the required contract
 * or is missing critical information.
 */
class InvalidEventException extends EventDispatcherException
{
    /**
     * Create a new InvalidEventException
     *
     * @param string $message The error message
     * @param ?string $eventType The event type that was invalid
     * @param int $code Error code
     */
    public function __construct(
        string $message,
        private ?string $eventType = null,
        int $code = self::INVALID_EVENT,
    ) {
        parent::__construct($message, $code);
    }

    /**
     * Get the event type that was invalid
     *
     * @return ?string
     */
    public function getEventType(): ?string
    {
        return $this->eventType;
    }
}
