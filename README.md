# LemonSqueezy PHP API Client

[![Tests](https://img.shields.io/github/actions/workflow/status/caspahouzer/lemonsqueezy-php-client/tests.yml)](https://github.com/caspahouzer/lemonsqueezy-php-client/actions) [![Packagist Version](https://img.shields.io/packagist/v/caspahouzer/lemonsqueezy-api-client)](https://packagist.org/packages/caspahouzer/lemonsqueezy-api-client) [![Packagist Downloads](https://img.shields.io/packagist/dt/caspahouzer/lemonsqueezy-api-client)](https://packagist.org/packages/caspahouzer/lemonsqueezy-api-client) [![PHP Version](https://img.shields.io/packagist/php-v/caspahouzer/lemonsqueezy-api-client)](https://packagist.org/packages/caspahouzer/lemonsqueezy-api-client) [![License](https://img.shields.io/packagist/l/caspahouzer/lemonsqueezy-api-client)](https://packagist.org/packages/caspahouzer/lemonsqueezy-api-client) [![BuyMeACoffee](https://raw.githubusercontent.com/pachadotdev/buymeacoffee-badges/main/bmc-donate-white.svg)](https://www.buymeacoffee.com/caspahouzer)

A modern, PSR-4 compliant PHP API client for the [LemonSqueezy](https://www.lemonsqueezy.com) platform. This package provides full coverage of all documented LemonSqueezy REST API endpoints with support for both bearer token authentication and public API access.

## Features

-   ✅ Full API coverage (19 resources)
-   ✅ PSR-4 autoloading, PSR-7/17/18 HTTP standards compliance
-   ✅ Bearer token authentication
-   ✅ Public License API support
-   ✅ Fluent query builder with filtering, sorting, pagination
-   ✅ Automatic rate limit handling (300 req/min)
-   ✅ Comprehensive exception hierarchy
-   ✅ Middleware-based request pipeline
-   ✅ JSON:API spec compliance
-   ✅ Webhook signature verification (HMAC-SHA256 with timing-safe comparison)
-   ✅ Webhook event listeners with dispatcher system
-   ✅ Batch operations for efficient bulk processing
-   ✅ Framework-agnostic (works with any PHP project)
-   ✅ Zero production dependencies (optional Guzzle fallback)

## Installation

### Via Composer (Recommended)

```bash
composer require caspahouzer/lemonsqueezy-api-client
```

### Manual Installation

1. Download or clone this repository
2. Add PSR-4 autoloading to your `composer.json`:
    ```json
    {
        "autoload": {
            "psr-4": {
                "LemonSqueezy\\": "path/to/LemonSqueezy/src/"
            }
        }
    }
    ```
3. Run `composer dump-autoload`

## Quick Start

### Basic Authentication

```php
use LemonSqueezy\ClientFactory;

// Create a client with your API key
$client = ClientFactory::create('YOUR_API_KEY');

// Get all customers
$customers = $client->customers()->list();

foreach ($customers as $customer) {
    echo $customer->getEmail() . "\n";
}

// Get a specific customer
$customer = $client->customers()->get('cust-123');

// Create a new discount
$discount = $client->discounts()->create([
    'name' => 'Summer Sale',
    'code' => 'SUMMER2024',
    'percent' => 20,
]);
```

### Public License API (No Authentication)

```php
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Client;

// Create client without API key (public API)
$config = (new ConfigBuilder())->build();
$client = new Client($config);

// Activate a license
$result = $client->licenseKeys()->activate(
    'license-key-here',
    'instance-name'
);

// Validate a license
$validation = $client->licenseKeys()->validate(
    'license-key-here',
    'instance-id-hash',
    'instance-name'
);

// Deactivate a license
$result = $client->licenseKeys()->deactivate(
    'license-key-here',
    'instance-id-hash',
    'instance-name'
);
```

## API Documentation

**[View Full API Documentation](https://caspahouzer.github.io/lemonsqueezy-php-client/)**

The comprehensive API documentation includes:

-   **Class reference** - Complete API reference for all public classes and methods
-   **Method signatures** - Detailed parameter and return type documentation
-   **Usage examples** - Code examples in class-level documentation
-   **Type hints** - Full PSR-5 compliant type hints for PHP 8.0+
-   **Cross-references** - Links between related classes and methods

Documentation is automatically generated and deployed to GitHub Pages on each release.

## Available Resources

The client provides access to **all 19 documented LemonSqueezy API resources**. Note that the LemonSqueezy API has specific limitations on which operations each resource supports:

| Resource              | Supported Methods                  | Endpoint                 | Notes                                       |
| --------------------- | ---------------------------------- | ------------------------ | ------------------------------------------- |
| Users                 | list, get                          | `/users`                 | Read-only                                   |
| Stores                | list, get                          | `/stores`                | Read-only                                   |
| Products              | list, get                          | `/products`              | Read-only                                   |
| Variants              | list, get                          | `/variants`              | Read-only                                   |
| Prices                | list, get                          | `/prices`                | Read-only                                   |
| Files                 | list, get                          | `/files`                 | Read-only                                   |
| Customers             | list, get, create, update          | `/customers`             | **Supports create/update only (no delete)** |
| Orders                | list, get                          | `/orders`                | Read-only                                   |
| Order Items           | list, get                          | `/order-items`           | Read-only                                   |
| Subscriptions         | list, get, update                  | `/subscriptions`         | **Supports update only (no create/delete)** |
| Subscription Invoices | list, get                          | `/subscription-invoices` | Read-only                                   |
| Subscription Items    | list, get, update                  | `/subscription-items`    | **Supports update only (no create/delete)** |
| Discounts             | list, get, create, update, delete  | `/discounts`             | **Full CRUD support**                       |
| Discount Redemptions  | list, get                          | `/discount-redemptions`  | Read-only                                   |
| **License Keys**      | **activate, validate, deactivate** | `/licenses/*`            | **Public API (no auth required)**           |
| Webhooks              | list, get, create, update, delete  | `/webhooks`              | **Full CRUD support**                       |
| Checkouts             | list, create                       | `/checkouts`             | **Supports create only (no update/delete)** |
| Affiliates            | list, get                          | `/affiliates`            | Read-only                                   |
| Usage Records         | list, get, create                  | `/usage-records`         | **Supports create only (no update/delete)** |

**→ See [docs/API_COVERAGE.md](docs/API_COVERAGE.md) for complete endpoint checklist with all methods and accurate API capability mapping**

## Query Building

Use the fluent query builder for advanced filtering, sorting, and pagination:

```php
use LemonSqueezy\Query\QueryBuilder;

$query = (new QueryBuilder())
    ->filter('status', 'active')
    ->filter('created_at', '2024-01-01', '>=')
    ->sort('created_at', 'desc')
    ->page(2)
    ->pageSize(50)
    ->include('subscriptions', 'orders');

$customers = $client->customers()->list($query);

// Check pagination
echo "Total: " . $customers->getTotal() . "\n";
echo "Page: " . $customers->getCurrentPage() . "\n";
echo "Has next: " . ($customers->hasNextPage() ? 'Yes' : 'No') . "\n";
```

## Error Handling

The client throws specific exceptions for different error conditions:

```php
use LemonSqueezy\Exception\{
    UnsupportedOperationException,
    RateLimitException,
    NotFoundException,
    UnauthorizedException,
    ValidationException,
    LemonSqueezyException
};

try {
    // This will throw UnsupportedOperationException because products are read-only
    $product = $client->products()->create(['name' => 'Product']);
} catch (UnsupportedOperationException $e) {
    echo "Operation not supported: " . $e->getMessage();
}

try {
    $order = $client->orders()->get('ord-nonexistent');
} catch (NotFoundException $e) {
    echo "Order not found: " . $e->getMessage();
} catch (RateLimitException $e) {
    $resetTime = $e->getResetTime();
    $seconds = $e->getSecondsUntilReset();
    echo "Rate limited. Reset in $seconds seconds";
} catch (UnauthorizedException $e) {
    echo "Invalid API key";
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    echo "Validation failed: " . json_encode($errors);
} catch (LemonSqueezyException $e) {
    echo "API error: " . $e->getMessage();
}
```

### Unsupported Operations

The LemonSqueezy API has specific limitations on which operations each resource supports. Attempting an unsupported operation will throw `UnsupportedOperationException`:

```php
use LemonSqueezy\Exception\UnsupportedOperationException;

try {
    // Read-only resources (cannot create, update, or delete)
    $client->products()->create(['name' => 'Product']);        // UnsupportedOperationException
    $client->users()->delete('user-123');                      // UnsupportedOperationException

    // Partially supported resources
    $client->subscriptions()->create([...]);                   // UnsupportedOperationException
    $client->customers()->delete('cust-123');                  // UnsupportedOperationException
    $client->checkouts()->update('checkout-123', [...]);       // UnsupportedOperationException
} catch (UnsupportedOperationException $e) {
    echo "This operation is not supported by the API: " . $e->getMessage();
    // Check docs/API_COVERAGE.md for which operations each resource supports
}
```

**Note:** Supported write operations are clearly marked in the [Available Resources](#available-resources) table above and in [docs/API_COVERAGE.md](docs/API_COVERAGE.md).

### Special API Operations

Some resources support special action endpoints beyond standard CRUD operations:

```php
// Orders: Generate Invoice
$invoice = $client->orders()->generateInvoice('ord-123');

// Orders: Issue Refund
$refund = $client->orders()->issueRefund('ord-123', [
    'refund_reason' => 'Customer requested refund'
]);

// Subscriptions: Cancel Subscription
$subscription = $client->subscriptions()->cancelSubscription('sub-456', [
    'reason' => 'Customer decided to cancel'
]);

// Subscription Items: Get Current Usage
$usage = $client->subscriptionItems()->getCurrentUsage('sub-item-789');
```

See [docs/API_COVERAGE.md](docs/API_COVERAGE.md#special-api-operations--) for all available special operations.

## Batch Operations

The client supports efficient bulk processing of resources through batch operations. Execute multiple create, update, and delete operations in a single batch with intelligent rate limiting.

### Quick Start

```php
use LemonSqueezy\Batch\Operations\BatchCreateOperation;
use LemonSqueezy\Batch\Operations\BatchUpdateOperation;
use LemonSqueezy\Batch\Operations\BatchDeleteOperation;

// Create multiple discounts
$result = $client->batchCreate('discounts', [
    [
        'store_id' => 123,
        'name' => 'Discount 1',
        'code' => 'DISC1',
        'amount' => 10,
        'amount_type' => 'percent'
    ],
    [
        'store_id' => 123,
        'name' => 'Discount 2',
        'code' => 'DISC2',
        'amount' => 20,
        'amount_type' => 'percent'
    ]
]);

// Check results
echo "Success: " . $result->getSuccessCount();
echo "Failed: " . $result->getFailureCount();
echo "Success Rate: " . $result->getSummary()['successRate'] . "%";
```

### Batch Methods

**1. Create Multiple Resources**

```php
// Using convenience method
$result = $client->batchCreate('customers', [
    ['email' => 'customer1@example.com', 'name' => 'Customer 1'],
    ['email' => 'customer2@example.com', 'name' => 'Customer 2'],
]);
```

**2. Update Multiple Resources**

```php
$result = $client->batchUpdate('customers', [
    ['id' => 'cust-1', 'name' => 'Updated Name 1'],
    ['id' => 'cust-2', 'name' => 'Updated Name 2'],
]);
```

**3. Delete Multiple Resources**

```php
$result = $client->batchDelete('customers', [
    'cust-1',
    'cust-2',
    'cust-3'
]);
```

**4. Mixed Operations**

```php
$operations = [
    new BatchCreateOperation('discounts', ['store_id' => 123, 'name' => 'New Discount', 'code' => 'NEW', 'amount' => 5, 'amount_type' => 'percent']),
    new BatchUpdateOperation('discounts', 'disc-1', ['name' => 'Updated Discount']),
    new BatchDeleteOperation('discounts', 'disc-2'),
];

$result = $client->batch($operations);
```

### Configuration Options

```php
$result = $client->batchCreate('customers', $items, [
    'delayMs' => 100,        // 100ms delay between operations
    'timeout' => 30,         // 30 second timeout per operation
    'stopOnError' => false,  // Continue on error (default: false)
]);
```

### Handling Results

```php
// Check overall status
if ($result->wasSuccessful()) {
    echo "All operations succeeded!";
}

// Get statistics
$summary = $result->getSummary();
echo "Total: " . $summary['totalRequested'];
echo "Success: " . $summary['successCount'];
echo "Failed: " . $summary['failureCount'];
echo "Success Rate: " . $summary['successRate'] . "%";
echo "Execution Time: " . $summary['executionTime'] . "s";

// Get successful operations
foreach ($result->getSuccessful() as $success) {
    echo "ID: " . $success['result']->id;
    echo "Status: " . $success['status'];
}

// Get failed operations
foreach ($result->getFailed() as $failure) {
    echo "Error: " . $failure['error'];
    echo "Details: " . json_encode($failure['details']);
}
```

### Rate Limiting

Batch operations automatically respect the API's 300 requests/minute rate limit:

-   Default delay: 200ms between operations (5 ops/sec)
-   Configurable via `delayMs` parameter
-   Operations execute sequentially to ensure compliance

## Advanced Configuration

### Custom HTTP Client

```php
use GuzzleHttp\Client as GuzzleClient;

$guzzleClient = new GuzzleClient(['timeout' => 60]);

$client = ClientFactory::create('YOUR_API_KEY')
    ->withHttpClient($guzzleClient)
    ->withTimeout(60)
    ->withMaxRetries(3)
    ->build();
```

### With Logger (PSR-3)

```php
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

$logger = new Logger('lemonsqueezy');
$logger->pushHandler(new StreamHandler('app.log'));

$client = ClientFactory::create('YOUR_API_KEY', $logger);
```

### Webhook Signature Verification

The client includes comprehensive webhook signature verification to securely validate incoming webhooks from LemonSqueezy. The verification uses HMAC-SHA256 with timing-safe comparison to prevent timing attacks.

#### Setup with Configuration

```php
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Client;

$config = (new ConfigBuilder())
    ->withApiKey('YOUR_API_KEY')
    ->withWebhookSecret('whk_secret_...') // Set your webhook secret from LemonSqueezy dashboard
    ->build();

$client = new Client($config);
```

#### Method 1: Using the Client Convenience Method

The simplest approach for integration:

```php
use LemonSqueezy\Exception\WebhookVerificationException;

// In your webhook endpoint
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

try {
    // Verify using client method
    $client->verifyWebhookSignature($payload, $signature);

    // Webhook is valid, process it
    $data = json_decode($payload, true);
    handleWebhook($data);

} catch (WebhookVerificationException $e) {
    http_response_code(401);
    exit('Webhook verification failed');
}
```

#### Method 2: Using WebhookVerifier Directly (Standalone)

For standalone use without a client instance:

```php
use LemonSqueezy\Webhook\WebhookVerifier;
use LemonSqueezy\Exception\WebhookVerificationException;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$webhookSecret = 'whk_secret_...';

try {
    // Throws exception on invalid signature
    WebhookVerifier::verify($payload, $signature, $webhookSecret);

    // Process webhook
    $data = json_decode($payload, true);
    handleWebhook($data);

} catch (WebhookVerificationException $e) {
    http_response_code(401);
    exit('Unauthorized');
}
```

#### Method 3: Using WebhookVerifier with Config

```php
use LemonSqueezy\Webhook\WebhookVerifier;
use LemonSqueezy\Configuration\ConfigBuilder;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

$config = (new ConfigBuilder())
    ->withApiKey('YOUR_API_KEY')
    ->withWebhookSecret('whk_secret_...')
    ->build();

try {
    WebhookVerifier::verifyWithConfig($payload, $signature, $config);
    // Process webhook
} catch (WebhookVerificationException $e) {
    http_response_code(401);
    exit('Webhook verification failed');
}
```

#### Method 4: Boolean Check (Non-Exception)

For cases where you prefer boolean returns:

```php
use LemonSqueezy\Webhook\WebhookVerifier;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$webhookSecret = 'whk_secret_...';

if (WebhookVerifier::isValid($payload, $signature, $webhookSecret)) {
    // Webhook is valid
    $data = json_decode($payload, true);
    handleWebhook($data);
} else {
    // Webhook verification failed
    http_response_code(401);
    exit('Invalid webhook signature');
}
```

#### Working with PSR-7 Streams

The verification supports PSR-7 StreamInterface for flexible webhook body handling:

```php
use LemonSqueezy\Webhook\WebhookVerifier;

// With a PSR-7 stream (e.g., from a framework like Laravel, Symfony)
$stream = $request->getBody(); // PSR-7 StreamInterface

if (WebhookVerifier::isValid($stream, $signature, $webhookSecret)) {
    // Process webhook
}
```

#### Exception Handling

Different error scenarios throw specific exception codes:

```php
use LemonSqueezy\Exception\WebhookVerificationException;

try {
    WebhookVerifier::verify($payload, $signature, $webhookSecret);
} catch (WebhookVerificationException $e) {
    match($e->getCode()) {
        WebhookVerificationException::MISSING_SECRET =>
            // Webhook secret not configured
            echo "Configuration error: webhook secret not set",
        WebhookVerificationException::EMPTY_SIGNATURE =>
            // Signature header missing or empty
            echo "Missing signature header",
        WebhookVerificationException::INVALID_FORMAT =>
            // Signature not in valid hex format
            echo "Invalid signature format",
        WebhookVerificationException::VERIFICATION_FAILED =>
            // Signature does not match
            echo "Webhook signature verification failed",
        WebhookVerificationException::UNSUPPORTED_ALGORITHM =>
            // Unsupported hash algorithm
            echo "Unsupported algorithm",
        default => echo "Unknown error"
    };
}
```

#### Security Features

-   **HMAC-SHA256**: Industry-standard cryptographic hash algorithm
-   **Timing-Safe Comparison**: Uses `hash_equals()` to prevent timing-based attacks
-   **Hex Digest Format**: Matches LemonSqueezy's standard webhook signature format
-   **Format Validation**: Validates signatures are 64-character hex strings

## Webhook Event Listeners

The framework includes a powerful event dispatcher system for handling webhooks. Register listeners for specific webhook events and they will be automatically executed when webhooks are received.

### Quick Start

```php
use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;
use LemonSqueezy\Webhook\Event\WebhookEvent;

// Register a listener for order creation
EventDispatcher::register('order.created', function($event) {
    $data = $event->getData();
    // Save order, send confirmation email, etc.
    echo "New order: {$data['id']}";
});

// In your webhook endpoint:
$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

try {
    $event = new WebhookEvent($body);
    $result = $client->dispatchWebhookEvent($body, $signature, $event);

    if ($result->hasFailures()) {
        http_response_code(202); // Accepted with some failures
    } else {
        http_response_code(200);
    }
} catch (WebhookVerificationException $e) {
    http_response_code(401);
}
```

### Features

-   **Event Dispatcher**: Central hub for listener registration and event dispatch
-   **Closure & Class Listeners**: Use closures for simple handlers or implement `EventListenerInterface` for complex logic
-   **Automatic Verification**: Webhook signature is automatically verified before dispatch
-   **Error Resilience**: Failures in one handler don't prevent others from executing
-   **Event Metadata**: Access verification status, timestamps, and raw webhook data
-   **Type-Safe**: Listener collections and registries prevent accidental misuse

### Supported Events

All LemonSqueezy webhook events are supported:

-   **Orders**: `order.created`, `order.refunded`
-   **Subscriptions**: `subscription.created`, `subscription.updated`, `subscription.expired`, `subscription.cancelled`
-   **License Keys**: `license-key.created`, `license-key.updated`, `license-key.expired`
-   **Invoices**: `subscription-invoice.created`, `subscription-invoice.paid`, `subscription-invoice.past-due`, `subscription-invoice.payment-attempt-failed`, `subscription-invoice.refunded`

**→ See [docs/WEBHOOKS.md](docs/WEBHOOKS.md) for comprehensive webhook listener documentation with examples**

### Examples

**Using Closures:**

```php
EventDispatcher::register('subscription.updated', function($event) {
    $subscription = $event->getData();
    // Update subscription in your database
});
```

**Using Listener Classes:**

```php
use LemonSqueezy\Webhook\Listener\EventListenerInterface;
use LemonSqueezy\Webhook\Event\EventInterface;

class OrderCreatedListener implements EventListenerInterface {
    public function handle(EventInterface $event): void {
        $data = $event->getData();
        // Process order creation
    }
}

EventDispatcher::register('order.created', new OrderCreatedListener());
```

**Multiple Listeners for One Event:**

```php
EventDispatcher::register('order.created', new SaveOrderListener());
EventDispatcher::register('order.created', new SendEmailListener());
EventDispatcher::register('order.created', new LogAnalyticsListener());
```

### Example: Complete Webhook Endpoint

See [examples/webhook_listener.php](examples/webhook_listener.php) for a complete working example.

## Response Models

All responses are hydrated into model objects with convenient property accessors:

```php
$customer = $client->customers()->get('cust-123');

echo $customer->getId();           // 'cust-123'
echo $customer->getEmail();        // 'customer@example.com'
echo $customer->getAttribute('name'); // Access any attribute

$attributes = $customer->getAttributes(); // Get all attributes
$meta = $customer->getMeta();      // Get meta information
```

## Rate Limiting

The client automatically tracks rate limits (300 requests per minute). If a rate limit is exceeded, a `RateLimitException` is thrown:

```php
try {
    // Make requests...
} catch (RateLimitException $e) {
    $remaining = $e->getRemainingRequests();
    $resetTime = $e->getResetTime();

    echo "Remaining requests: $remaining\n";
    echo "Reset time: " . $resetTime->format('Y-m-d H:i:s') . "\n";

    // Wait and retry
    sleep($e->getSecondsUntilReset());
}
```

## Testing

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run with coverage
composer test:coverage

# Static analysis
composer stan

# Fix code style
composer cs:fix
```

## Requirements

-   PHP >= 8.0
-   PSR-18 HTTP Client (or GuzzleHttp 7.0+)
-   PSR-17 HTTP Factories (or GuzzleHttp 7.0+)
-   PSR-7 HTTP Messages

## License

MIT. See [LICENSE](LICENSE) file for details.

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/caspahouzer/lemonsqueezy-php-client).

## API Documentation

For complete API documentation, visit [LemonSqueezy API Docs](https://docs.lemonsqueezy.com/api).
