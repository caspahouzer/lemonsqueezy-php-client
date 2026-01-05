<?php

namespace LemonSqueezy\Http\Middleware;

use LemonSqueezy\Http\RateLimiter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientInterface;

/**
 * Middleware for rate limit handling
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(private RateLimiter $rateLimiter) {}

    /**
     * Record request and check rate limit before sending
     */
    public function process(RequestInterface $request, ClientInterface $client): ResponseInterface
    {
        $this->rateLimiter->recordRequest();

        return $client->sendRequest($request);
    }
}
