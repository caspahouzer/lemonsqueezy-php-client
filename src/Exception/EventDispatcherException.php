<?php

namespace LemonSqueezy\Exception;

/**
 * Exception thrown during event dispatcher operations
 *
 * Base exception for all event dispatcher-related errors.
 */
class EventDispatcherException extends LemonSqueezyException
{
    public const NO_LISTENERS = 1001;
    public const INVALID_EVENT = 1002;
    public const LISTENER_FAILED = 1003;
}
