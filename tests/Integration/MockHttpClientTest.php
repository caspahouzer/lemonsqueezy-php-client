<?php

namespace LemonSqueezy\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Mock HTTP Client for testing without real API calls
 */
class MockHttpClient implements ClientInterface
{
    private array $responses = [];
    private int $responseIndex = 0;

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if (!isset($this->responses[$this->responseIndex])) {
            throw new \Exception('No mock response configured for request');
        }

        return $this->responses[$this->responseIndex++];
    }

    public function queueResponse(ResponseInterface $response): self
    {
        $this->responses[] = $response;
        return $this;
    }

    public function reset(): self
    {
        $this->responses = [];
        $this->responseIndex = 0;
        return $this;
    }
}

/**
 * Mock PSR-7 Response for testing
 */
class MockResponse implements ResponseInterface
{
    public function __construct(
        private int $statusCode,
        private array $headers,
        private string $body
    ) {}

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
        $this->headers[$name] = [$value];
        return $this;
    }

    public function withAddedHeader($name, $value): self
    {
        return $this;
    }

    public function withoutHeader($name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    public function getBody()
    {
        return new MockStream($this->body);
    }

    public function withBody($body): self
    {
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return '';
    }
}

/**
 * Mock PSR-7 Stream for testing
 */
class MockStream
{
    public function __construct(private string $content) {}

    public function __toString(): string
    {
        return $this->content;
    }

    public function getSize(): ?int
    {
        return strlen($this->content);
    }
}

/**
 * Integration tests with mocked HTTP responses
 */
class MockHttpClientTest extends TestCase
{
    /**
     * Test mocking customer list response
     */
    public function testMockCustomerListResponse(): void
    {
        $mockResponse = new MockResponse(
            200,
            ['Content-Type' => ['application/vnd.api+json']],
            json_encode([
                'data' => [
                    [
                        'id' => 'cust-1',
                        'type' => 'customers',
                        'attributes' => [
                            'name' => 'John Doe',
                            'email' => 'john@example.com',
                        ],
                    ],
                    [
                        'id' => 'cust-2',
                        'type' => 'customers',
                        'attributes' => [
                            'name' => 'Jane Smith',
                            'email' => 'jane@example.com',
                        ],
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 2,
                        'current_page' => 1,
                        'per_page' => 50,
                        'last_page' => 1,
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $mockResponse->getStatusCode());
        $body = json_decode($mockResponse->getBody());
        $this->assertCount(2, $body->data);
    }

    /**
     * Test mocking customer create response
     */
    public function testMockCustomerCreateResponse(): void
    {
        $mockResponse = new MockResponse(
            201,
            ['Content-Type' => ['application/vnd.api+json']],
            json_encode([
                'data' => [
                    'id' => 'cust-new',
                    'type' => 'customers',
                    'attributes' => [
                        'name' => 'New Customer',
                        'email' => 'new@example.com',
                    ],
                ],
            ])
        );

        $this->assertEquals(201, $mockResponse->getStatusCode());
        $body = json_decode($mockResponse->getBody());
        $this->assertEquals('cust-new', $body->data->id);
    }

    /**
     * Test mocking product CRUD responses
     */
    public function testMockProductResponses(): void
    {
        // List response
        $listResponse = new MockResponse(200, [], json_encode([
            'data' => [
                ['id' => 'prod-1', 'attributes' => ['name' => 'Product 1']],
                ['id' => 'prod-2', 'attributes' => ['name' => 'Product 2']],
            ],
        ]));

        // Get response
        $getResponse = new MockResponse(200, [], json_encode([
            'data' => ['id' => 'prod-1', 'attributes' => ['name' => 'Product 1']],
        ]));

        // Create response
        $createResponse = new MockResponse(201, [], json_encode([
            'data' => ['id' => 'prod-3', 'attributes' => ['name' => 'Product 3']],
        ]));

        // Update response
        $updateResponse = new MockResponse(200, [], json_encode([
            'data' => ['id' => 'prod-1', 'attributes' => ['name' => 'Updated Product']],
        ]));

        // Delete response
        $deleteResponse = new MockResponse(204, [], '');

        $this->assertEquals(200, $listResponse->getStatusCode());
        $this->assertEquals(200, $getResponse->getStatusCode());
        $this->assertEquals(201, $createResponse->getStatusCode());
        $this->assertEquals(200, $updateResponse->getStatusCode());
        $this->assertEquals(204, $deleteResponse->getStatusCode());
    }

    /**
     * Test mocking error responses
     */
    public function testMockErrorResponses(): void
    {
        // 404 Not Found
        $notFoundResponse = new MockResponse(404, [], json_encode([
            'errors' => [
                ['status' => '404', 'detail' => 'Resource not found'],
            ],
        ]));

        // 401 Unauthorized
        $unauthorizedResponse = new MockResponse(401, [], json_encode([
            'errors' => [
                ['status' => '401', 'detail' => 'Unauthorized'],
            ],
        ]));

        // 429 Rate Limited
        $rateLimitResponse = new MockResponse(429, [], json_encode([
            'errors' => [
                ['status' => '429', 'detail' => 'Rate limit exceeded'],
            ],
        ]));

        // 422 Validation Error
        $validationResponse = new MockResponse(422, [], json_encode([
            'errors' => [
                ['status' => '422', 'detail' => 'Validation failed'],
            ],
        ]));

        $this->assertEquals(404, $notFoundResponse->getStatusCode());
        $this->assertEquals(401, $unauthorizedResponse->getStatusCode());
        $this->assertEquals(429, $rateLimitResponse->getStatusCode());
        $this->assertEquals(422, $validationResponse->getStatusCode());
    }

    /**
     * Test mocking license key responses
     */
    public function testMockLicenseKeyResponses(): void
    {
        // Activate response
        $activateResponse = new MockResponse(200, [], json_encode([
            'instance_id' => 'inst-abc123',
            'times_activated' => 1,
            'times_activated_max' => 5,
        ]));

        // Validate response
        $validateResponse = new MockResponse(200, [], json_encode([
            'valid' => true,
            'times_activated' => 1,
            'times_activated_max' => 5,
        ]));

        // Deactivate response
        $deactivateResponse = new MockResponse(200, [], json_encode([
            'times_activated' => 0,
        ]));

        $activate = json_decode($activateResponse->getBody());
        $this->assertEquals('inst-abc123', $activate->instance_id);

        $validate = json_decode($validateResponse->getBody());
        $this->assertTrue($validate->valid);

        $deactivate = json_decode($deactivateResponse->getBody());
        $this->assertEquals(0, $deactivate->times_activated);
    }

    /**
     * Test mocking paginated responses
     */
    public function testMockPaginatedResponses(): void
    {
        $paginatedResponse = new MockResponse(200, [], json_encode([
            'data' => array_map(fn($i) => [
                'id' => "cust-$i",
                'attributes' => ['name' => "Customer $i"],
            ], range(1, 25)),
            'meta' => [
                'pagination' => [
                    'total' => 100,
                    'current_page' => 1,
                    'per_page' => 25,
                    'last_page' => 4,
                ],
            ],
        ]));

        $body = json_decode($paginatedResponse->getBody());
        $this->assertCount(25, $body->data);
        $this->assertEquals(100, $body->meta->pagination->total);
        $this->assertEquals(1, $body->meta->pagination->current_page);
        $this->assertEquals(4, $body->meta->pagination->last_page);
    }

    /**
     * Test mocking all resource types
     */
    public function testMockAllResourceTypes(): void
    {
        $resourceTypes = [
            'users',
            'stores',
            'products',
            'variants',
            'prices',
            'files',
            'customers',
            'orders',
            'order-items',
            'subscriptions',
            'subscription-invoices',
            'subscription-items',
            'discounts',
            'discount-redemptions',
            'webhooks',
            'checkouts',
            'affiliates',
        ];

        foreach ($resourceTypes as $resourceType) {
            $response = new MockResponse(200, [], json_encode([
                'data' => [
                    'id' => 'test-1',
                    'type' => $resourceType,
                    'attributes' => ['name' => 'Test'],
                ],
            ]));

            $this->assertEquals(200, $response->getStatusCode());
            $body = json_decode($response->getBody());
            $this->assertEquals($resourceType, $body->data->type);
        }
    }

    /**
     * Test mock client queueing
     */
    public function testMockClientQueueing(): void
    {
        $mockClient = new MockHttpClient();

        $response1 = new MockResponse(200, [], json_encode(['data' => 'first']));
        $response2 = new MockResponse(200, [], json_encode(['data' => 'second']));
        $response3 = new MockResponse(200, [], json_encode(['data' => 'third']));

        $mockClient->queueResponse($response1);
        $mockClient->queueResponse($response2);
        $mockClient->queueResponse($response3);

        // Test that responses are returned in order
        $mockRequest = new MockRequest();

        $result1 = $mockClient->sendRequest($mockRequest);
        $body1 = json_decode($result1->getBody());
        $this->assertEquals('first', $body1->data);

        $result2 = $mockClient->sendRequest($mockRequest);
        $body2 = json_decode($result2->getBody());
        $this->assertEquals('second', $body2->data);

        $result3 = $mockClient->sendRequest($mockRequest);
        $body3 = json_decode($result3->getBody());
        $this->assertEquals('third', $body3->data);
    }

    /**
     * Test mock client reset
     */
    public function testMockClientReset(): void
    {
        $mockClient = new MockHttpClient();
        $response = new MockResponse(200, [], '{}');

        $mockClient->queueResponse($response);
        $mockClient->reset();

        // After reset, should have no responses
        $this->expectException(\Exception::class);
        $mockRequest = new MockRequest();
        $mockClient->sendRequest($mockRequest);
    }
}

/**
 * Mock PSR-7 Request for testing
 */
class MockRequest implements RequestInterface
{
    public function getRequestTarget(): string
    {
        return '/';
    }
    public function withRequestTarget($requestTarget): self
    {
        return $this;
    }
    public function getMethod(): string
    {
        return 'GET';
    }
    public function withMethod($method): self
    {
        return $this;
    }
    public function getUri()
    {
        return null;
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
    public function getBody()
    {
        return null;
    }
    public function withBody($body): self
    {
        return $this;
    }
}
