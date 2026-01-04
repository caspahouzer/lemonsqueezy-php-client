<?php

namespace LemonSqueezy\Http\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientInterface;

/**
 * Contract for HTTP middleware
 */
interface MiddlewareInterface
{
    /**
     * Process a request and response
     *
     * @param RequestInterface $request The HTTP request
     * @param ClientInterface $client The HTTP client to delegate to
     * @return ResponseInterface The HTTP response
     */
    public function process(RequestInterface $request, ClientInterface $client): ResponseInterface;
}
