<?php

namespace LemonSqueezy\Exception;

/**
 * Exception for webhook signature verification failures
 */
class WebhookVerificationException extends ValidationException
{
    public const MISSING_SECRET = 1001;
    public const EMPTY_SIGNATURE = 1002;
    public const INVALID_FORMAT = 1003;
    public const VERIFICATION_FAILED = 1004;
    public const UNSUPPORTED_ALGORITHM = 1005;
}
