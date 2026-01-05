<?php

namespace LemonSqueezy\Tests\Unit;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Mock HTTP Client for unit tests
 */
class MockHttpClient implements ClientInterface
{
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return new MockResponse(200, [], '{"data": []}');
    }
}

/**
 * Mock PSR-17 Request Factory
 */
class MockRequestFactory
{
    public function createRequest(string $method, $uri)
    {
        return new MockRequest($method, (string) $uri);
    }
}

/**
 * Mock PSR-17 Stream Factory
 */
class MockStreamFactory
{
    public function createStream(string $content = '')
    {
        return new MockStream($content);
    }
    public function createStreamFromFile(string $filename, string $mode = 'r')
    {
        return new MockStream('');
    }
    public function createStreamFromResource($resource)
    {
        return new MockStream('');
    }
}

/**
 * Mock PSR-7 Request
 */
class MockRequest implements RequestInterface
{
    public function __construct(private string $method, private string $uri) {}
    public function getRequestTarget(): string
    {
        return $this->uri;
    }
    public function withRequestTarget($requestTarget): self
    {
        return $this;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    public function withMethod($method): self
    {
        return $this;
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function withUri($uri, $preserveHost = false): self
    {
        return $this;
    }
    public function getProtocolVersion(): string
    {
        return '1.1';
    }
    public function withProtocolVersion($version): self
    {
        return $this;
    }
    public function getHeaders(): array
    {
        return [];
    }
    public function hasHeader($name): bool
    {
        return false;
    }
    public function getHeader($name): array
    {
        return [];
    }
    public function getHeaderLine($name): string
    {
        return '';
    }
    public function withHeader($name, $value): self
    {
        return $this;
    }
    public function withAddedHeader($name, $value): self
    {
        return $this;
    }
    public function withoutHeader($name): self
    {
        return $this;
    }
    public function getBody(): StreamInterface
    {
        return new MockStream('');
    }
    public function withBody(StreamInterface $body): self
    {
        return $this;
    }
}

/**
 * Mock PSR-7 Response
 */
class MockResponse implements ResponseInterface
{
    public function __construct(
        private int $statusCode,
        private array $headers,
        private string $body
    ) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    public function getReasonPhrase(): string
    {
        return '';
    }
    public function withStatus($code, $reasonPhrase = ''): self
    {
        return $this;
    }
    public function getProtocolVersion(): string
    {
        return '1.1';
    }
    public function withProtocolVersion($version): self
    {
        return $this;
    }
    public function getHeaders(): array
    {
        return $this->headers;
    }
    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }
    public function getHeader($name): array
    {
        return $this->headers[$name] ?? [];
    }
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }
    public function withHeader($name, $value): self
    {
        return $this;
    }
    public function withAddedHeader($name, $value): self
    {
        return $this;
    }
    public function withoutHeader($name): self
    {
        return $this;
    }
    public function getBody(): StreamInterface
    {
        return new MockStream($this->body);
    }
    public function withBody(StreamInterface $body): self
    {
        return $this;
    }
}

/**
 * Mock PSR-7 Stream
 */
class MockStream implements StreamInterface
{
    public function __construct(private string $content) {}
    public function __toString(): string
    {
        return $this->content;
    }
    public function close(): void {}
    public function detach()
    {
        return null;
    }
    public function getSize(): ?int
    {
        return strlen($this->content);
    }
    public function tell(): int
    {
        return 0;
    }
    public function eof(): bool
    {
        return true;
    }
    public function isSeekable(): bool
    {
        return false;
    }
    public function seek($offset, $whence = SEEK_SET): void {}
    public function rewind(): void {}
    public function isWritable(): bool
    {
        return false;
    }
    public function write($string): int
    {
        return 0;
    }
    public function isReadable(): bool
    {
        return true;
    }
    public function read($length): string
    {
        return $this->content;
    }
    public function getContents(): string
    {
        return $this->content;
    }
    public function getMetadata($key = null)
    {
        return null;
    }
}
