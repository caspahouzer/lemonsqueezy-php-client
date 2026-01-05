<?php

namespace LemonSqueezy\Http;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Factory for creating PSR-7 requests
 */
class RequestFactory
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    /**
     * Create a PSR-7 request
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $uri Request URI
     * @param ?array $data Request body data (will be JSON encoded)
     * @param array $headers Additional headers
     */
    public function create(
        string $method,
        string $uri,
        ?array $data = null,
        array $headers = []
    ): RequestInterface {
        $request = $this->requestFactory->createRequest($method, $uri);

        // Add headers
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        // Add body if data provided
        if ($data !== null) {
            $body = $this->streamFactory->createStream(json_encode($data));
            $request = $request->withBody($body);

            // Ensure Content-Type is set for requests with body
            if (!$request->hasHeader('Content-Type')) {
                $request = $request->withHeader('Content-Type', 'application/json');
            }
        }

        return $request;
    }

    /**
     * Create a stream from a string
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }
}
