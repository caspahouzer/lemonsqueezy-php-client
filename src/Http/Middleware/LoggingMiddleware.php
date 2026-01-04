<?php

declare(strict_types=1);

namespace LemonSqueezy\Http\Middleware;

use LemonSqueezy\Logger\LoggerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(RequestInterface $request, ClientInterface $client): ResponseInterface
    {
        $this->logger->info(sprintf('Request: %s %s', $request->getMethod(), $request->getUri()));

        $response = $client->sendRequest($request);

        $this->logger->info(sprintf('Response: %s', $response->getStatusCode()));

        return $response;
    }
}
