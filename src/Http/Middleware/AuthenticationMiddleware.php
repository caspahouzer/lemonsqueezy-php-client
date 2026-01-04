<?php

namespace LemonSqueezy\Http\Middleware;

use LemonSqueezy\Authentication\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientInterface;

/**
 * HTTP middleware for applying authentication and required headers
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(private AuthenticationInterface $authentication)
    {
    }

    /**
     * Apply authentication and JSON:API headers to request
     */
    public function process(RequestInterface $request, ClientInterface $client): ResponseInterface
    {
        // Apply authentication strategy (Bearer token, public, etc.)
        $request = $this->authentication->authenticate($request);

        // Ensure JSON:API headers are present
        if (!$request->hasHeader('Accept')) {
            $request = $request->withHeader('Accept', 'application/vnd.api+json');
        }

        if (!$request->hasHeader('Content-Type') && $request->getBody()->getSize() > 0) {
            $request = $request->withHeader('Content-Type', 'application/vnd.api+json');
        }

        return $client->sendRequest($request);
    }
}
