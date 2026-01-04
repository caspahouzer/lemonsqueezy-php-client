# Quick Start Guide

Get up and running with the LemonSqueezy PHP API client in 5 minutes.

## 1. Install

```bash
# Navigate to the package directory
cd /Applications/XAMPP/xamppfiles/htdocs/plugins/base/LemonSqueezy

# Install dependencies
composer install
```

## 2. Get Your API Key

1. Log in to [LemonSqueezy](https://app.lemonsqueezy.com)
2. Go to Settings → API
3. Create a new API key or copy existing one:
   - **For Testing/Development**: Create an API key in **Test Mode** (won't affect live data)
   - **For Production**: Create an API key in **Live Mode**
4. Copy your API key

## 3. Create Your First Client

```php
<?php
require 'vendor/autoload.php';

use LemonSqueezy\ClientFactory;

// Create a client with your API key
$client = ClientFactory::create('YOUR_API_KEY');

// Verify it works
$users = $client->users()->list();
echo "Success! Found " . $users->getTotal() . " users.\n";
```

## 4. Common Operations

### List Customers
```php
$customers = $client->customers()->list();

foreach ($customers->items() as $customer) {
    echo $customer->getEmail() . "\n";
}
```

### Get Single Item
```php
$customer = $client->customers()->get('cust-123');
echo $customer->getEmail();
```

### Create Item
```php
$discount = $client->discounts()->create([
    'name' => 'Summer Sale',
    'code' => 'SUMMER2024',
    'percent' => 20,
]);
echo "Created: " . $discount->getId();
```

### Update Item
```php
$updated = $client->discounts()->update('disc-123', [
    'name' => 'Updated Name',
    'percent' => 25,
]);
```

### Delete Item
```php
$client->discounts()->delete('disc-123');
```

## 5. Filtering and Pagination

```php
use LemonSqueezy\Query\QueryBuilder;

$query = (new QueryBuilder())
    ->filter('status', 'active')
    ->sort('created_at', 'desc')
    ->page(1)
    ->pageSize(50);

$customers = $client->customers()->list($query);

echo "Page: " . $customers->getCurrentPage() . "\n";
echo "Total: " . $customers->getTotal() . "\n";
```

## 6. Error Handling

```php
use LemonSqueezy\Exception\{
    NotFoundException,
    RateLimitException,
    UnauthorizedException
};

try {
    $customer = $client->customers()->get('nonexistent');
} catch (NotFoundException $e) {
    echo "Customer not found\n";
} catch (RateLimitException $e) {
    echo "Rate limited, wait: " . $e->getSecondsUntilReset() . "s\n";
} catch (UnauthorizedException $e) {
    echo "Check your API key\n";
}
```

## 7. Public License API (No Auth Required)

```php
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Client;

// Create client without API key
$config = (new ConfigBuilder())->build();
$client = new Client($config);

// Activate a license
$result = $client->licenseKeys()->activate(
    'license-key',
    'example.com'
);

echo "Activated! Instance ID: " . $result['instance_id'];
```

## Available Resources

Access any of these resources via `$client->resourceName()`:

- `users()`, `stores()`, `products()`, `variants()`, `prices()`, `files()`
- `customers()`, `orders()`, `orderItems()`
- `subscriptions()`, `subscriptionInvoices()`, `subscriptionItems()`
- `discounts()`, `discountRedemptions()`, `licenseKeys()`
- `webhooks()`, `checkouts()`, `affiliates()`

## Need Help?

- **Full Documentation**: See [README.md](../README.md)
- **Installation**: See [INSTALLATION.md](INSTALLATION.md)
- **Examples**: Check [examples/](../examples/) folder
- **API Docs**: Visit [LemonSqueezy API Docs](https://docs.lemonsqueezy.com/api)

## Troubleshooting

### Invalid API key?
```
UnauthorizedException: Check your API key in LemonSqueezy dashboard
```

### Rate limited?
```php
catch (RateLimitException $e) {
    sleep($e->getSecondsUntilReset());
    // Retry request
}
```

### GuzzleHttp not installed?
```bash
composer require guzzlehttp/guzzle
```

### HTTP client not found?
You need PSR-18 HTTP client. Install Guzzle or provide your own via config.

---

That's it! You're ready to use the LemonSqueezy API. 🚀
