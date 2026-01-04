# Installation Guide

## Via Composer (Recommended)

The easiest way to install the LemonSqueezy PHP API client is via Composer.

### Prerequisites

-   PHP 8.0 or higher
-   Composer installed on your system
-   GuzzleHttp 7.0+ or another PSR-18 compatible HTTP client

### Installation Steps

1. **Install the package**

    ```bash
    composer require slk/lemonsqueezy-api-client
    ```

2. **Verify installation**

    ```bash
    vendor/bin/phpunit tests/Unit/ClientTest.php
    ```

## Manual Installation

If you prefer to install manually or don't use Composer:

1. **Clone or download the repository**

    ```bash
    git clone https://github.com/caspahouzer/lemonsqueezy-php-client.git
    cd LemonSqueezy
    ```

2. **Configure autoloading**

    Add this to your `composer.json` (create one if it doesn't exist):

    ```json
    {
        "autoload": {
            "psr-4": {
                "LemonSqueezy\\": "path/to/LemonSqueezy/src/"
            }
        }
    }
    ```

3. **Install dependencies**

    ```bash
    composer install
    ```

4. **Load in your project**

    ```php
    require 'vendor/autoload.php';
    ```

## Configuration

### Environment Variables

Store your API key in environment variables rather than hardcoding:

```php
// .env (for development/testing - use Test Mode API key)
LEMONSQUEEZY_API_KEY=your_test_mode_api_key

// For production, use a Live Mode API key instead
```

**Note**: For development and testing, use an API key created in Test Mode. This way your tests won't affect live data.

### Loading from Environment

```php
use LemonSqueezy\ClientFactory;

$apiKey = getenv('LEMONSQUEEZY_API_KEY');
$client = ClientFactory::create($apiKey);
```

### With PSR-7 Environment Variable Support

```php
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Client;

$config = (new ConfigBuilder())
    ->withApiKey($_ENV['LEMONSQUEEZY_API_KEY'] ?? '')
    ->withTimeout(30)
    ->withMaxRetries(3)
    ->build();

$client = new Client($config);
```

## Verify Installation

Create a test script to verify everything works:

```php
<?php
require 'vendor/autoload.php';

use LemonSqueezy\ClientFactory;

try {
    $client = ClientFactory::create('YOUR_API_KEY');
    $users = $client->users()->list();
    echo "✓ Installation successful! Found " . $users->getTotal() . " users.\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
```

Run the test:

```bash
php verify.php
```

## Troubleshooting

### "No PSR-18 HTTP client provided and GuzzleHttp is not installed"

You need to install an HTTP client. The easiest solution is:

```bash
composer require guzzlehttp/guzzle
```

Or use your own PSR-18 compatible client:

```php
use LemonSqueezy\Configuration\ConfigBuilder;
use MyHttpClient\Client;

$config = (new ConfigBuilder())
    ->withApiKey('YOUR_API_KEY')
    ->withHttpClient(new Client())
    ->build();
```

### "Class not found" errors

Make sure Composer autoloading is properly configured:

```bash
composer dump-autoload
```

### Connection timeouts

If you're experiencing timeout issues, increase the timeout:

```php
$config = (new ConfigBuilder())
    ->withApiKey('YOUR_API_KEY')
    ->withTimeout(60)
    ->build();
```

### Rate limiting errors

The client automatically tracks rate limits. If you hit the limit:

```php
use LemonSqueezy\Exception\RateLimitException;

try {
    $customers = $client->customers()->list();
} catch (RateLimitException $e) {
    sleep($e->getSecondsUntilReset());
    $customers = $client->customers()->list();
}
```

## Next Steps

-   Read the [Quick Start](QUICKSTART.md) guide
-   Check out [examples](../examples/) folder
-   Review [API Resources documentation](API_RESOURCES.md)
-   See [Error Handling](ERROR_HANDLING.md) guide
