# LemonSqueezy PHP API Client

A modern, PSR-4 compliant PHP API client for the [LemonSqueezy](https://www.lemonsqueezy.com) platform. This package provides full coverage of all documented LemonSqueezy REST API endpoints with support for both bearer token authentication and public API access.

## Features

- ✅ Full API coverage (19 resources)
- ✅ PSR-4 autoloading, PSR-7/17/18 HTTP standards compliance
- ✅ Bearer token authentication
- ✅ Public License API support
- ✅ Fluent query builder with filtering, sorting, pagination
- ✅ Automatic rate limit handling (300 req/min)
- ✅ Comprehensive exception hierarchy
- ✅ Middleware-based request pipeline
- ✅ JSON:API spec compliance
- ✅ Framework-agnostic (works with any PHP project)
- ✅ Zero production dependencies (optional Guzzle fallback)

## Installation

### Via Composer (Recommended)

```bash
composer require slk/lemonsqueezy-api-client
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

The client provides access to **all 18 documented LemonSqueezy API resources** with comprehensive method coverage:

| Resource | Methods | Endpoint | Notes |
|----------|---------|----------|-------|
| Users | list, get | `/users` | Read-only |
| Stores | ✅ CRUD | `/stores` | Full support |
| Products | ✅ CRUD | `/products` | Full support |
| Variants | ✅ CRUD | `/variants` | Full support |
| Prices | ✅ CRUD | `/prices` | Full support |
| Files | ✅ CRUD | `/files` | Full support |
| Customers | ✅ CRUD | `/customers` | Full support |
| Orders | list, get | `/orders` | Read-only |
| Order Items | list, get | `/order-items` | Read-only |
| Subscriptions | list, get | `/subscriptions` | Read-only |
| Subscription Invoices | list, get | `/subscription-invoices` | Read-only |
| Subscription Items | list, get | `/subscription-items` | Read-only |
| Discounts | ✅ CRUD | `/discounts` | Full support |
| Discount Redemptions | list, get | `/discount-redemptions` | Read-only |
| **License Keys** | **activate**, **validate**, **deactivate** | `/licenses/*` | **Public API (no auth)** |
| Webhooks | ✅ CRUD | `/webhooks` | Full support |
| Checkouts | list, create | `/checkouts` | Limited |
| Affiliates | list, get | `/affiliates` | Read-only |

**→ See [API_COVERAGE.md](API_COVERAGE.md) for complete endpoint checklist with all methods and status**

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
    RateLimitException,
    NotFoundException,
    UnauthorizedException,
    ValidationException,
    LemonSqueezyException
};

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

- PHP >= 8.0
- PSR-18 HTTP Client (or GuzzleHttp 7.0+)
- PSR-17 HTTP Factories (or GuzzleHttp 7.0+)
- PSR-7 HTTP Messages

## License

MIT. See [LICENSE](LICENSE) file for details.

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/slk/lemonsqueezy-php-client).

## API Documentation

For complete API documentation, visit [LemonSqueezy API Docs](https://docs.lemonsqueezy.com/api).
