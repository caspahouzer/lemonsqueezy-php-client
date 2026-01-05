# LemonSqueezy PHP API Client

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

## Available Resources

The client provides access to **all 18 documented LemonSqueezy API resources**. Note that the LemonSqueezy API has specific limitations on which operations each resource supports:

| Resource              | Supported Methods              | Endpoint                 | Notes                                          |
| --------------------- | ------------------------------ | ------------------------ | ---------------------------------------------- |
| Users                 | list, get                      | `/users`                 | Read-only                                      |
| Stores                | list, get                      | `/stores`                | Read-only                                      |
| Products              | list, get                      | `/products`              | Read-only                                      |
| Variants              | list, get                      | `/variants`              | Read-only                                      |
| Prices                | list, get                      | `/prices`                | Read-only                                      |
| Files                 | list, get                      | `/files`                 | Read-only                                      |
| **Customers**         | **list, get, create, update**  | `/customers`             | **Supports create/update only (no delete)**    |
| Orders                | list, get                      | `/orders`                | Read-only                                      |
| Order Items           | list, get                      | `/order-items`           | Read-only                                      |
| Subscriptions         | list, get, update              | `/subscriptions`         | **Supports update only (no create/delete)**    |
| Subscription Invoices | list, get                      | `/subscription-invoices` | Read-only                                      |
| Subscription Items    | list, get, update              | `/subscription-items`    | **Supports update only (no create/delete)**    |
| Discounts             | list, get, create, update, delete | `/discounts`          | **Full CRUD support**                          |
| Discount Redemptions  | list, get                      | `/discount-redemptions`  | Read-only                                      |
| **License Keys**      | **activate, validate, deactivate** | `/licenses/*`        | **Public API (no auth required)**              |
| Webhooks              | list, get, create, update, delete | `/webhooks`          | **Full CRUD support**                          |
| Checkouts             | list, create                   | `/checkouts`             | **Supports create only (no update/delete)**    |
| Affiliates            | list, get                      | `/affiliates`            | Read-only                                      |

**→ See [API_COVERAGE.md](API_COVERAGE.md) for complete endpoint checklist with all methods and accurate API capability mapping**

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
    // Check API_COVERAGE.md for which operations each resource supports
}
```

**Note:** Supported write operations are clearly marked in the [Available Resources](#available-resources) table above and in [API_COVERAGE.md](API_COVERAGE.md).

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

See [API_COVERAGE.md](API_COVERAGE.md#special-api-operations--) for all available special operations.

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

### Webhook Verification

```php
$config = (new ConfigBuilder())
    ->withApiKey('YOUR_API_KEY')
    ->withWebhookSecret('webhook_secret_...')
    ->build();

$client = new Client($config);
$secret = $client->getConfig()->getWebhookSecret();

// Verify webhook signature
// Implementation depends on LemonSqueezy's signature format
```

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
