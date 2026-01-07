<?php

namespace LemonSqueezy\Exception;

/**
 * Exception thrown when an event listener fails
 *
 * Wraps exceptions from event handlers with context information.
 */
class ListenerException extends EventDispatcherException
{
    /**
     * Create a new ListenerException
     *
     * @param string $message The error message
     * @param ?string $eventType The event type being processed
     * @param ?\Throwable $previous The previous exception from the listener
     */
    public function __construct(
        string $message,
        private ?string $eventType = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, self::LISTENER_FAILED, $previous);
    }

    /**
     * Get the event type that was being processed
     *
     * @return ?string
     */
    public function getEventType(): ?string
    {
        return $this->eventType;
    }
}
