<?php

namespace LemonSqueezy\Authentication;

use LemonSqueezy\Configuration\Credentials;
use Psr\Http\Message\RequestInterface;

/**
 * Bearer token authentication strategy for LemonSqueezy API
 */
class BearerTokenAuth implements AuthenticationInterface
{
    public function __construct(private Credentials $credentials)
    {
    }

    /**
     * Apply Bearer token authentication to request
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        $apiKey = $this->credentials->getApiKey();

        if (!$apiKey) {
            return $request;
        }

        return $request->withHeader('Authorization', "Bearer {$apiKey}");
    }

    /**
     * Check if API key is configured
     */
    public function isValid(): bool
    {
        return $this->credentials->hasApiKey();
    }

    /**
     * Get the name of this authentication strategy
     */
    public function getName(): string
    {
        return 'Bearer Token';
    }
}
