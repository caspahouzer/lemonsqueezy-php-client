# Webhook Event Listeners Guide

The LemonSqueezy Framework now includes a built-in event dispatcher and listener system for handling webhooks. This allows you to easily register handlers for specific webhook events and process them in your application.

## Overview

The webhook listener system provides:

- **Event Dispatcher**: Central hub for registering and dispatching webhook events
- **Listener Registration**: Register handlers as closures or classes
- **Automatic Signature Verification**: Built-in webhook signature validation
- **Type-Safe Collections**: Structured listener management
- **Event Metadata**: Track verification status, timestamps, and execution info
- **Error Handling**: Graceful failure handling with detailed error information

## Quick Start

### 1. Register Event Listeners

Register a listener for a specific webhook event:

```php
use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;

// Register with a closure
EventDispatcher::register('order.created', function($event) {
    $data = $event->getData();
    echo "Order {$data['id']} was created";
});

// Register with a listener class
EventDispatcher::register('subscription.updated', new SubscriptionUpdateListener());
```

### 2. Create Your Webhook Endpoint

Create a PHP script that receives webhook requests:

```php
<?php
use LemonSqueezy\Webhook\Event\WebhookEvent;
use LemonSqueezy\ClientFactory;

// Initialize the LemonSqueezy client
$client = ClientFactory::create($_ENV['LEMONSQUEEZY_API_KEY'])
    ->withWebhookSecret($_ENV['LEMONSQUEEZY_WEBHOOK_SECRET']);

// Get the webhook payload and signature
$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

try {
    // Create the webhook event
    $event = new WebhookEvent($body);

    // Verify signature and dispatch to listeners
    $result = $client->dispatchWebhookEvent($body, $signature, $event);

    // Check if all handlers succeeded
    if ($result->hasFailures()) {
        // Some handlers failed
        http_response_code(202); // Accepted (partial success)
        foreach ($result->getFailures() as $failure) {
            error_log("Handler failed: " . $failure['error']->getMessage());
        }
    } else {
        // All handlers succeeded
        http_response_code(200);
    }
} catch (WebhookVerificationException $e) {
    // Signature verification failed
    http_response_code(401);
    error_log("Webhook verification failed: " . $e->getMessage());
}
?>
```

## Listener Registration

### Using Closures

The simplest way to register a listener is with a closure:

```php
EventDispatcher::register('order.created', function($event) {
    $data = $event->getData();
    // Process the order
});
```

### Using Listener Classes

For more complex handlers, create a class implementing `EventListenerInterface`:

```php
use LemonSqueezy\Webhook\Listener\EventListenerInterface;
use LemonSqueezy\Webhook\Event\EventInterface;

class OrderCreatedListener implements EventListenerInterface
{
    public function handle(EventInterface $event): void
    {
        $data = $event->getData();

        // Process order creation
        $this->saveOrder($data);
        $this->sendConfirmationEmail($data);
        $this->logAnalytics($data);
    }

    private function saveOrder($data) { /* ... */ }
    private function sendConfirmationEmail($data) { /* ... */ }
    private function logAnalytics($data) { /* ... */ }
}

// Register the listener
EventDispatcher::register('order.created', new OrderCreatedListener());
```

### Registering Multiple Listeners

You can register multiple listeners for the same event:

```php
EventDispatcher::register('order.created', new SaveOrderListener());
EventDispatcher::register('order.created', new SendEmailListener());
EventDispatcher::register('order.created', new LogAnalyticsListener());
```

All listeners will be executed sequentially, and failures in one listener won't prevent others from executing.

## Webhook Events

### Supported Event Types

The LemonSqueezy Framework supports these webhook events:

- `order.created` - When a new order is created
- `order.refunded` - When an order is refunded
- `subscription.created` - When a subscription is created
- `subscription.updated` - When a subscription is updated
- `subscription.expired` - When a subscription expires
- `subscription.cancelled` - When a subscription is cancelled
- `license-key.created` - When a license key is created
- `license-key.updated` - When a license key is updated
- `license-key.expired` - When a license key expires
- `subscription-invoice.created` - When an invoice is created
- `subscription-invoice.paid` - When an invoice is paid
- `subscription-invoice.past-due` - When an invoice is past due
- `subscription-invoice.payment-attempt-failed` - When a payment attempt fails
- `subscription-invoice.refunded` - When an invoice is refunded

### Accessing Event Data

Inside your listener, access event information:

```php
EventDispatcher::register('order.created', function($event) {
    // Get the event type
    $eventType = $event->getEventType(); // 'order.created'

    // Get the raw data (JSON:API format)
    $rawData = $event->getRawData();

    // Get deserialized data
    $data = $event->getData();

    // Get webhook metadata (event_name, webhook_id, etc.)
    $webhookMeta = $event->getWebhookMeta();

    // Get included resources
    $included = $event->getIncluded();

    // Check if webhook was verified
    $isVerified = $event->isVerified();

    // Get event metadata (timestamp, algorithm, etc.)
    $metadata = $event->getMetadata();
});
```

## Error Handling

### Handling Handler Failures

When a listener throws an exception, the dispatcher catches it and continues with remaining listeners:

```php
use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;

EventDispatcher::register('order.created', function($event) {
    throw new Exception('Handler error');
});

EventDispatcher::register('order.created', function($event) {
    // This will still execute even if the first handler failed
    echo "Second handler still runs";
});

$dispatcher = new EventDispatcher();
$result = $dispatcher->dispatch($event);

// Check for failures
if ($result->hasFailures()) {
    foreach ($result->getFailures() as $failure) {
        $error = $failure['error'];
        echo "Handler failed: " . $error->getMessage();
    }
}
```

### Dispatch Result

The `dispatch()` method returns a `DispatchResult` object with execution information:

```php
$dispatcher = new EventDispatcher();
$result = $dispatcher->dispatch($event);

// Check results
echo "Event type: " . $result->getEventType();
echo "Handlers run: " . $result->getHandlerCount();
echo "Successful: " . $result->getSuccessCount();
echo "Failed: " . $result->getFailureCount();
echo "All succeeded: " . ($result->allSucceeded() ? 'yes' : 'no');

// Get failure details
if ($result->hasFailures()) {
    foreach ($result->getFailures() as $failure) {
        $handler = $failure['handler'];
        $error = $failure['error'];

        // Log error details
        error_log("Handler {$handler} failed: {$error->getMessage()}");
    }
}

// Convert to array for logging/debugging
$array = $result->toArray();
```

## Webhook Signature Verification

### Configuration

Set your webhook secret when creating the client:

```php
use LemonSqueezy\ClientFactory;

$client = ClientFactory::create($_ENV['LEMONSQUEEZY_API_KEY'])
    ->withWebhookSecret($_ENV['LEMONSQUEEZY_WEBHOOK_SECRET']);
```

### Verification

The `dispatchWebhookEvent()` method automatically verifies the signature before dispatching:

```php
try {
    $result = $client->dispatchWebhookEvent($body, $signature, $event);
    // Signature verified and event dispatched
} catch (WebhookVerificationException $e) {
    // Signature verification failed
    http_response_code(401);
}
```

### Manual Verification

If you need to verify signatures separately:

```php
use LemonSqueezy\Webhook\WebhookVerifier;

try {
    WebhookVerifier::verify($body, $signature, $secret);
    // Signature is valid
} catch (WebhookVerificationException $e) {
    // Signature verification failed
}

// Or use the client method
try {
    $client->verifyWebhookSignature($body, $signature);
} catch (WebhookVerificationException $e) {
    // Verification failed
}
```

## Advanced Usage

### Using Custom Event Registries

Create isolated listener registries for different parts of your application:

```php
use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;
use LemonSqueezy\Webhook\Listener\EventRegistry;

// Create a custom registry
$registry = new EventRegistry();

// Register listeners with custom registry
$registry->register('order.created', function($event) {
    echo "Custom handler";
});

// Dispatch with custom registry
$dispatcher = new EventDispatcher($registry);
$result = $dispatcher->dispatch($event);
```

### Creating Event Factories

Use `EventFactory` for more control over event creation:

```php
use LemonSqueezy\Webhook\Event\Factory\EventFactory;

// From JSON
$event = EventFactory::createFromJson($jsonPayload, 'order.created');

// From array
$event = EventFactory::createFromArray($payload);

// From file (useful for testing)
$event = EventFactory::createFromFile('fixtures/order-created.json');

// With metadata
$event = EventFactory::createWithMetadata(
    $jsonPayload,
    'order.created',
    isVerified: true,
    algorithm: 'sha256'
);
```

### Deserializing to Entity Models

Access deserialized entity models:

```php
use LemonSqueezy\Webhook\Deserializer\EventPayloadDeserializer;

$deserializer = new EventPayloadDeserializer();

EventDispatcher::register('order.created', function($event) use ($deserializer) {
    $rawData = $event->getRawData();

    // Deserialize to proper model
    $order = $deserializer->deserialize($rawData);

    // Now you have a proper Order model instance
    echo $order->getId(); // Access model methods
});
```

## Testing

### Creating Test Events

For testing, easily create webhook events from fixtures:

```php
use LemonSqueezy\Webhook\Event\WebhookEvent;

// From JSON string
$payload = file_get_contents('fixtures/order-created.json');
$event = new WebhookEvent($payload);

// From array
$payload = [
    'meta' => ['event_name' => 'order.created'],
    'data' => ['type' => 'orders', 'id' => 'ord-123'],
];
$event = WebhookEvent::fromArray($payload);

// With custom metadata
$metadata = new EventMetadata(new DateTime(), isVerified: true);
$event = new WebhookEvent($payload, null, $metadata);
```

### Testing Listeners

```php
use PHPUnit\Framework\TestCase;
use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;

class OrderCreatedListenerTest extends TestCase
{
    protected function setUp(): void
    {
        EventDispatcher::clearAll();
    }

    public function testOrderCreatedListener(): void
    {
        $called = false;

        EventDispatcher::register('order.created', function($event) use (&$called) {
            $called = true;
        });

        $event = WebhookEvent::fromArray([
            'meta' => ['event_name' => 'order.created'],
            'data' => ['type' => 'orders', 'id' => 'ord-123'],
        ]);

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($event);

        $this->assertTrue($called);
        $this->assertTrue($result->allSucceeded());
    }
}
```

## Exception Handling

The webhook listener system throws specific exceptions:

- `EventDispatcherException` - Base exception for dispatcher errors
- `ListenerException` - Thrown when a listener fails
- `InvalidEventException` - Thrown for invalid event data
- `UnregisteredEventException` - Thrown when no listeners are registered
- `WebhookVerificationException` - Thrown when signature verification fails

```php
use LemonSqueezy\Exception\{
    EventDispatcherException,
    ListenerException,
    InvalidEventException,
    UnregisteredEventException
};
use LemonSqueezy\Exception\WebhookVerificationException;

try {
    $result = $client->dispatchWebhookEvent($body, $signature, $event);
} catch (WebhookVerificationException $e) {
    // Handle signature verification failure
} catch (EventDispatcherException $e) {
    // Handle dispatcher errors
}
```

## Best Practices

1. **Keep listeners lightweight**: Move heavy processing to background jobs
2. **Handle errors gracefully**: Check `$result->hasFailures()` and log appropriately
3. **Return HTTP 200/202 promptly**: LemonSqueezy may retry failed webhooks
4. **Use listener classes for complex logic**: Keep closure-based listeners simple
5. **Log webhook processing**: Track all webhook events for debugging
6. **Validate data**: Don't assume webhook data structure is always as expected
7. **Verify signatures**: Always verify webhook signatures for security
8. **Register listeners early**: Register all listeners at application startup

## Examples

### Example 1: Order Notification

```php
// Handle new orders
EventDispatcher::register('order.created', function($event) {
    $data = $event->getData();

    // Save order to database
    $order = Order::create($data);

    // Send confirmation email
    Mail::send(new OrderConfirmation($order));

    // Log analytics
    Analytics::track('order_created', ['order_id' => $order->id]);
});
```

### Example 2: Subscription Management

```php
// Handle subscription updates
EventDispatcher::register('subscription.updated', function($event) {
    $data = $event->getData();

    // Update subscription in database
    $subscription = Subscription::findOrFail($data['id']);
    $subscription->update($data);

    // Update user permissions based on subscription status
    updateUserPermissions($subscription);
});

// Handle subscription expiration
EventDispatcher::register('subscription.expired', function($event) {
    $data = $event->getData();

    // Revoke access
    $user = User::find($data['customer_id']);
    $user->revoke_access = true;
    $user->save();

    // Send renewal reminder
    Mail::send(new SubscriptionExpiredNotice($user));
});
```

### Example 3: License Key Tracking

```php
// Track license key creation
EventDispatcher::register('license-key.created', function($event) {
    $data = $event->getData();

    // Store license key information
    LicenseKey::create([
        'external_id' => $data['id'],
        'key' => $data['attributes']['key'],
        'activation_limit' => $data['attributes']['activation_limit'],
    ]);
});
```

## Troubleshooting

### Listeners Not Being Called

1. **Check registration**: Ensure listeners are registered for the correct event type
2. **Verify event type**: Check the `meta.event_name` in the webhook payload
3. **Check initialization**: Make sure listeners are registered before webhook is received
4. **Clear cache**: Call `EventDispatcher::clearAll()` if reusing dispatcher

### Signature Verification Failing

1. **Check secret**: Verify webhook secret matches LemonSqueezy configuration
2. **Use correct header**: Webhook signature is in `X-Signature` header
3. **Stream handling**: Use `php://input` for request body, not `$_POST`
4. **Timing attacks**: Framework uses `hash_equals()` to prevent timing attacks

### Handlers Not Completing

1. **Check timeouts**: HTTP request timeout may be too short
2. **Use queuing**: For long operations, dispatch to background jobs
3. **Check logs**: Review error logs for exception details
4. **Test manually**: Run listener functions directly for debugging

## Version History

- **v1.2.2** - Added webhook event listener system
  - Event dispatcher for webhook event handling
  - Listener registration and execution
  - Event metadata and deserialization
  - Comprehensive exception hierarchy
  - Full unit and integration tests
