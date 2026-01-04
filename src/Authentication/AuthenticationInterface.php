<?php

namespace LemonSqueezy\Authentication;

use Psr\Http\Message\RequestInterface;

/**
 * Contract for authentication strategies
 */
interface AuthenticationInterface
{
    /**
     * Apply authentication to a request
     *
     * @param RequestInterface $request The HTTP request to authenticate
     * @return RequestInterface The authenticated request
     */
    public function authenticate(RequestInterface $request): RequestInterface;

    /**
     * Check if this authentication strategy has valid credentials
     */
    public function isValid(): bool;

    /**
     * Get the name of this authentication strategy
     */
    public function getName(): string;
}
