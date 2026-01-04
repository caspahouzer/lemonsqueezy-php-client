<?php

namespace LemonSqueezy\Authentication;

use Psr\Http\Message\RequestInterface;

/**
 * Public API authentication (no credentials required)
 * Used for LemonSqueezy public License API endpoints
 */
class PublicAuth implements AuthenticationInterface
{
    /**
     * No authentication needed for public API
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request;
    }

    /**
     * Public API always has valid "credentials" (none needed)
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * Get the name of this authentication strategy
     */
    public function getName(): string
    {
        return 'Public API';
    }
}
