<?php

namespace LemonSqueezy;

use LemonSqueezy\Configuration\Config;
use LemonSqueezy\Authentication\{AuthenticationInterface, BearerTokenAuth, PublicAuth};
use LemonSqueezy\Http\{RequestFactory, ResponseHandler, RateLimiter};
use LemonSqueezy\Http\Middleware\{MiddlewareInterface, AuthenticationMiddleware, RateLimitMiddleware, LoggingMiddleware, CacheMiddleware};
use LemonSqueezy\Cache\FileCache;
use LemonSqueezy\Logger\NullLogger;
use LemonSqueezy\Batch\{BatchOperationExecutor, BatchResult};
use LemonSqueezy\Batch\Operations\{BatchCreateOperation, BatchUpdateOperation, BatchDeleteOperation};
use LemonSqueezy\Webhook\WebhookVerifier;
use LemonSqueezy\Exception\WebhookVerificationException;
use Psr\Http\Message\StreamInterface;
use LemonSqueezy\Resource\{
    Users,
    Stores,
    Products,
    Variants,
    Prices,
    Files,
    Customers,
    Orders,
    OrderItems,
    Subscriptions,
    SubscriptionInvoices,
    SubscriptionItems,
    Discounts,
    DiscountRedemptions,
    LicenseKeys,
    Webhooks,
    Checkouts,
    Affiliates
};
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Main LemonSqueezy API client facade
 *
 * Example usage:
 * ```php
 * $client = new Client($config);
 * $customers = $client->customers()->list();
 * $user = $client->users()->get('usr-123');
 * ```
 */
class Client
{
    private RequestFactory $requestFactory;
    private ResponseHandler $responseHandler;
    private RateLimiter $rateLimiter;
    private ClientInterface $httpClient;
    private AuthenticationInterface $authentication;
    private LoggerInterface $logger;
    private array $middleware = [];
    private ?BatchOperationExecutor $batchExecutor = null;

    // Cached resource instances
    private ?Users $users = null;
    private ?Stores $stores = null;
    private ?Products $products = null;
    private ?Variants $variants = null;
    private ?Prices $prices = null;
    private ?Files $files = null;
    private ?Customers $customers = null;
    private ?Orders $orders = null;
    private ?OrderItems $orderItems = null;
    private ?Subscriptions $subscriptions = null;
    private ?SubscriptionInvoices $subscriptionInvoices = null;
    private ?SubscriptionItems $subscriptionItems = null;
    private ?Discounts $discounts = null;
    private ?DiscountRedemptions $discountRedemptions = null;
    private ?LicenseKeys $licenseKeys = null;
    private ?Webhooks $webhooks = null;
    private ?Checkouts $checkouts = null;
    private ?Affiliates $affiliates = null;

    /**
     * Create a new LemonSqueezy API client
     *
     * @param Config $config Configuration object
     * @param ?LoggerInterface $logger Optional PSR-3 logger
     */
    public function __construct(
        private Config $config,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->rateLimiter = new RateLimiter();
        $this->responseHandler = new ResponseHandler();
        $this->httpClient = $this->initializeHttpClient();
        $this->requestFactory = $this->initializeRequestFactory();
        $this->authentication = $this->initializeAuthentication();

        $this->setupDefaultMiddleware();
    }

    /**
     * Initialize the HTTP client (PSR-18)
     */
    private function initializeHttpClient(): ClientInterface
    {
        if ($this->config->getHttpClient()) {
            return $this->config->getHttpClient();
        }

        // Try to auto-detect and use Guzzle if available
        try {
            $guzzle = new \GuzzleHttp\Client();

            return new class ($guzzle) implements ClientInterface {
                public function __construct(private \GuzzleHttp\Client $client) {}

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    return $this->client->send($request);
                }
            };
        } catch (\Throwable) {
            throw new \RuntimeException(
                'No PSR-18 HTTP client provided and GuzzleHttp is not installed. '
                    . 'Either provide an HTTP client via Config or install guzzlehttp/guzzle.'
            );
        }
    }

    /**
     * Initialize the request factory (PSR-17)
     */
    private function initializeRequestFactory(): RequestFactory
    {
        $requestFactory = $this->config->getRequestFactory();
        $streamFactory = $this->config->getStreamFactory();

        // Try to use provided factories, or auto-detect Guzzle
        if ($requestFactory && $streamFactory) {
            return new RequestFactory($requestFactory, $streamFactory);
        }

        try {
            $guzzleFactory = new \GuzzleHttp\Psr7\HttpFactory();

            return new RequestFactory($guzzleFactory, $guzzleFactory);
        } catch (\Throwable) {
            throw new \RuntimeException(
                'No PSR-17 factories provided and GuzzleHttp is not installed. '
                    . 'Either provide factories via Config or install guzzlehttp/guzzle.'
            );
        }
    }

    /**
     * Initialize authentication strategy
     */
    private function initializeAuthentication(): AuthenticationInterface
    {
        if ($this->config->isAuthenticated()) {
            return new BearerTokenAuth($this->config->getCredentials());
        }

        return new PublicAuth();
    }

    /**
     * Setup default middleware stack
     */
    private function setupDefaultMiddleware(): void
    {
        $this->middleware = [
            new CacheMiddleware(new FileCache(sys_get_temp_dir() . '/lemonsqueezy-cache')),
            new LoggingMiddleware($this->logger),
            new AuthenticationMiddleware($this->authentication),
            new RateLimitMiddleware($this->rateLimiter),
        ];
    }

    /**
     * Add middleware to the stack
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Execute a batch of operations
     *
     * Takes an array of BatchOperation objects and executes them sequentially,
     * respecting rate limits and collecting results.
     *
     * Example usage:
     * ```php
     * $operations = [
     *     new BatchCreateOperation('products', ['name' => 'Product 1']),
     *     new BatchCreateOperation('products', ['name' => 'Product 2']),
     * ];
     * $result = $client->batch($operations);
     * ```
     *
     * @param array $operations Array of BatchOperation objects
     * @param array $options Execution options (delayMs, timeout, stopOnError, etc.)
     * @return BatchResult The result of the batch execution
     */
    public function batch(array $operations, array $options = []): BatchResult
    {
        $executor = $this->getBatchExecutor();
        return $executor->execute($operations, $options);
    }

    /**
     * Execute a batch of create operations
     *
     * Convenience method for creating multiple resources.
     *
     * Example usage:
     * ```php
     * $items = [
     *     ['name' => 'Product 1'],
     *     ['name' => 'Product 2'],
     *     ['name' => 'Product 3'],
     * ];
     * $result = $client->batchCreate('products', $items);
     * ```
     *
     * @param string $resource The resource type (e.g., 'products', 'customers')
     * @param array $items Array of data arrays to create
     * @param array $options Execution options
     * @return BatchResult The result of the batch creation
     */
    public function batchCreate(string $resource, array $items, array $options = []): BatchResult
    {
        $operations = array_map(
            fn($data) => new BatchCreateOperation($resource, $data),
            $items
        );

        return $this->batch($operations, $options);
    }

    /**
     * Execute a batch of update operations
     *
     * Convenience method for updating multiple resources.
     *
     * Example usage:
     * ```php
     * $items = [
     *     ['id' => 'prod-1', 'name' => 'Updated 1'],
     *     ['id' => 'prod-2', 'name' => 'Updated 2'],
     * ];
     * $result = $client->batchUpdate('products', $items);
     * ```
     *
     * @param string $resource The resource type (e.g., 'products', 'customers')
     * @param array $items Array of update arrays (must contain 'id' key)
     * @param array $options Execution options
     * @return BatchResult The result of the batch update
     */
    public function batchUpdate(string $resource, array $items, array $options = []): BatchResult
    {
        $operations = array_map(
            fn($data) => new BatchUpdateOperation(
                $resource,
                $data['id'] ?? throw new \InvalidArgumentException('Each update item must contain an "id" key'),
                array_diff_key($data, ['id' => null])
            ),
            $items
        );

        return $this->batch($operations, $options);
    }

    /**
     * Execute a batch of delete operations
     *
     * Convenience method for deleting multiple resources.
     *
     * Example usage:
     * ```php
     * $ids = ['prod-1', 'prod-2', 'prod-3'];
     * $result = $client->batchDelete('products', $ids);
     * ```
     *
     * @param string $resource The resource type (e.g., 'products', 'customers')
     * @param array $ids Array of resource IDs to delete
     * @param array $options Execution options
     * @return BatchResult The result of the batch deletion
     */
    public function batchDelete(string $resource, array $ids, array $options = []): BatchResult
    {
        $operations = array_map(
            fn($id) => new BatchDeleteOperation($resource, $id),
            $ids
        );

        return $this->batch($operations, $options);
    }

    /**
     * Verify a webhook signature using the configured webhook secret
     *
     * Convenience method that automatically uses the webhook secret from the Config object.
     * Throws an exception if verification fails.
     *
     * Example usage:
     * ```php
     * try {
     *     $client->verifyWebhookSignature(
     *         file_get_contents('php://input'),
     *         $_SERVER['HTTP_X_SIGNATURE'] ?? ''
     *     );
     *     // Signature is valid - process webhook
     * } catch (WebhookVerificationException $e) {
     *     http_response_code(401);
     * }
     * ```
     *
     * @param string|StreamInterface $body The webhook request body
     * @param string $signature The signature from webhook header
     * @param string $algorithm The hash algorithm to use (default: sha256)
     * @throws WebhookVerificationException If verification fails or secret is not configured
     */
    public function verifyWebhookSignature(
        string|StreamInterface $body,
        string $signature,
        string $algorithm = 'sha256'
    ): void {
        WebhookVerifier::verifyWithConfig($body, $signature, $this->config, $algorithm);
    }

    /**
     * Get or create the batch operation executor
     */
    private function getBatchExecutor(): BatchOperationExecutor
    {
        return $this->batchExecutor ??= new BatchOperationExecutor($this);
    }

    /**
     * Send an HTTP request through the middleware stack
     */
    public function request(
        string $method,
        string $endpoint,
        ?array $data = null,
        array $headers = []
    ): array {
        $uri = $this->config->getApiBaseUrl() . '/' . ltrim($endpoint, '/');
        $request = $this->requestFactory->create($method, $uri, $data, $headers);

        // Process through middleware stack
        try {
            $response = $this->executeMiddlewareStack($request);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Guzzle throws RequestException for HTTP errors
            // Convert to our response handler which will throw appropriate exceptions
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw new \LemonSqueezy\Exception\HttpException(
                    $e->getMessage(),
                    0,
                    []
                );
            }
        }

        // Handle response and convert to array
        return $this->responseHandler->handle($response);
    }

    /**
     * Execute the middleware stack
     */
    private function executeMiddlewareStack(RequestInterface $request): ResponseInterface
    {
        // Create a client wrapper that can handle remaining middleware
        $client = new class ($this->httpClient, $this->middleware) implements ClientInterface {
            private int $index = 0;

            public function __construct(
                private ClientInterface $baseClient,
                private array $middlewares
            ) {}

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                if ($this->index >= count($this->middlewares)) {
                    return $this->baseClient->sendRequest($request);
                }

                $middleware = $this->middlewares[$this->index++];

                return $middleware->process($request, $this);
            }
        };

        return $client->sendRequest($request);
    }

    // Resource accessors

    /**
     * Get the Users resource
     */
    public function users(): Users
    {
        return $this->users ??= new Users($this);
    }

    /**
     * Get the Stores resource
     */
    public function stores(): Stores
    {
        return $this->stores ??= new Stores($this);
    }

    /**
     * Get the Products resource
     */
    public function products(): Products
    {
        return $this->products ??= new Products($this);
    }

    /**
     * Get the Variants resource
     */
    public function variants(): Variants
    {
        return $this->variants ??= new Variants($this);
    }

    /**
     * Get the Prices resource
     */
    public function prices(): Prices
    {
        return $this->prices ??= new Prices($this);
    }

    /**
     * Get the Files resource
     */
    public function files(): Files
    {
        return $this->files ??= new Files($this);
    }

    /**
     * Get the Customers resource
     */
    public function customers(): Customers
    {
        return $this->customers ??= new Customers($this);
    }

    /**
     * Get the Orders resource
     */
    public function orders(): Orders
    {
        return $this->orders ??= new Orders($this);
    }

    /**
     * Get the Order Items resource
     */
    public function orderItems(): OrderItems
    {
        return $this->orderItems ??= new OrderItems($this);
    }

    /**
     * Get the Subscriptions resource
     */
    public function subscriptions(): Subscriptions
    {
        return $this->subscriptions ??= new Subscriptions($this);
    }

    /**
     * Get the Subscription Invoices resource
     */
    public function subscriptionInvoices(): SubscriptionInvoices
    {
        return $this->subscriptionInvoices ??= new SubscriptionInvoices($this);
    }

    /**
     * Get the Subscription Items resource
     */
    public function subscriptionItems(): SubscriptionItems
    {
        return $this->subscriptionItems ??= new SubscriptionItems($this);
    }

    /**
     * Get the Discounts resource
     */
    public function discounts(): Discounts
    {
        return $this->discounts ??= new Discounts($this);
    }

    /**
     * Get the Discount Redemptions resource
     */
    public function discountRedemptions(): DiscountRedemptions
    {
        return $this->discountRedemptions ??= new DiscountRedemptions($this);
    }

    /**
     * Get the License Keys resource (public API)
     */
    public function licenseKeys(): LicenseKeys
    {
        return $this->licenseKeys ??= new LicenseKeys($this);
    }

    /**
     * Get the Webhooks resource
     */
    public function webhooks(): Webhooks
    {
        return $this->webhooks ??= new Webhooks($this);
    }

    /**
     * Get the Checkouts resource
     */
    public function checkouts(): Checkouts
    {
        return $this->checkouts ??= new Checkouts($this);
    }

    /**
     * Get the Affiliates resource
     */
    public function affiliates(): Affiliates
    {
        return $this->affiliates ??= new Affiliates($this);
    }

    /**
     * Get the configuration
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get the logger
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
