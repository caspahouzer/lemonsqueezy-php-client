<?php

/**
 * Webhook Event Listener Example
 *
 * This example demonstrates how to set up webhook listeners for LemonSqueezy events
 * and handle them in your application.
 *
 * To use this in production:
 * 1. Set up a public endpoint for receiving webhooks
 * 2. Configure your LemonSqueezy dashboard to send webhooks to this endpoint
 * 3. Set the LEMONSQUEEZY_WEBHOOK_SECRET environment variable
 */

require __DIR__ . '/../vendor/autoload.php';

use LemonSqueezy\ClientFactory;
use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;
use LemonSqueezy\Webhook\Event\WebhookEvent;
use LemonSqueezy\Webhook\Listener\EventListenerInterface;
use LemonSqueezy\Webhook\Event\EventInterface;
use LemonSqueezy\Exception\WebhookVerificationException;

// ============================================================================
// Example 1: Register listeners with closures
// ============================================================================

echo "=== Example 1: Closure-Based Listeners ===\n";

EventDispatcher::register('order.created', function ($event) {
    echo "✓ Order created webhook received\n";
    echo "  Event type: " . $event->getEventType() . "\n";
    echo "  Data: " . json_encode($event->getData()) . "\n";
});

EventDispatcher::register('subscription.updated', function ($event) {
    echo "✓ Subscription updated webhook received\n";
    echo "  Event type: " . $event->getEventType() . "\n";
});

// ============================================================================
// Example 2: Create listener classes for complex logic
// ============================================================================

echo "\n=== Example 2: Listener Classes ===\n";

class OrderCreatedListener implements EventListenerInterface
{
    public function handle(EventInterface $event): void
    {
        echo "✓ OrderCreatedListener handling event\n";

        $data = $event->getData();

        if (is_array($data)) {
            echo "  - Order ID: " . ($data['id'] ?? 'N/A') . "\n";
            echo "  - Type: " . ($data['type'] ?? 'N/A') . "\n";
        }

        // In production, you would:
        // 1. Save order to database
        // 2. Send confirmation email
        // 3. Trigger fulfillment workflow
        // 4. Update inventory
    }
}

class SubscriptionUpdatedListener implements EventListenerInterface
{
    public function handle(EventInterface $event): void
    {
        echo "✓ SubscriptionUpdatedListener handling event\n";

        // In production, you would:
        // 1. Update subscription status in database
        // 2. Update user permissions
        // 3. Sync with your application
    }
}

EventDispatcher::register('order.created', new OrderCreatedListener());
EventDispatcher::register('subscription.updated', new SubscriptionUpdatedListener());

// ============================================================================
// Example 3: Register multiple listeners for the same event
// ============================================================================

echo "\n=== Example 3: Multiple Listeners ===\n";

EventDispatcher::register('order.created', function ($event) {
    echo "✓ Email notification listener\n";
});

EventDispatcher::register('order.created', function ($event) {
    echo "✓ Analytics tracking listener\n";
});

EventDispatcher::register('order.created', function ($event) {
    echo "✓ Webhook forwarding listener\n";
});

// ============================================================================
// Example 4: Simulate receiving a webhook and dispatching it
// ============================================================================

echo "\n=== Example 4: Processing Webhook (Simulation) ===\n";

// Simulate webhook payload from LemonSqueezy
$webhookPayload = [
    'meta' => [
        'event_name' => 'order.created',
        'webhook_id' => 'webhook-abc123',
    ],
    'data' => [
        'type' => 'orders',
        'id' => 'ord-12345',
        'attributes' => [
            'total' => 99.99,
            'currency' => 'USD',
            'status' => 'completed',
            'customer_id' => 'cust-67890',
        ],
    ],
    'included' => [],
];

// Create event from webhook payload
$event = WebhookEvent::fromArray($webhookPayload);

// Dispatch to registered listeners
$dispatcher = new EventDispatcher();
$result = $dispatcher->dispatch($event);

// Check results
echo "Dispatch Results:\n";
echo "  - Event type: " . $result->getEventType() . "\n";
echo "  - Handlers run: " . $result->getHandlerCount() . "\n";
echo "  - Successful: " . $result->getSuccessCount() . "\n";
echo "  - Failed: " . $result->getFailureCount() . "\n";
echo "  - All succeeded: " . ($result->allSucceeded() ? 'Yes' : 'No') . "\n";

// ============================================================================
// Example 5: Real webhook endpoint (HTTP handling)
// ============================================================================

if (php_sapi_name() === 'cli') {
    echo "\n=== Example 5: HTTP Webhook Endpoint (Code Demonstration) ===\n";
    echo "This example shows how to handle real HTTP webhook requests:\n\n";

    $code = <<<'PHP'
<?php
// webhook-handler.php - Public endpoint for LemonSqueezy webhooks

use LemonSqueezy\ClientFactory;
use LemonSqueezy\Webhook\Event\WebhookEvent;
use LemonSqueezy\Exception\WebhookVerificationException;

// Initialize client with webhook secret
$client = ClientFactory::create($_ENV['LEMONSQUEEZY_API_KEY'])
    ->withWebhookSecret($_ENV['LEMONSQUEEZY_WEBHOOK_SECRET']);

// Register your listeners early in application boot
register_webhook_listeners();

// Get the raw request body and signature header
$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

try {
    // Create event from webhook payload
    $event = new WebhookEvent($body);

    // Verify signature and dispatch to listeners
    // This handles both verification and dispatch in one call
    $result = $client->dispatchWebhookEvent($body, $signature, $event);

    // Check if all handlers succeeded
    if ($result->hasFailures()) {
        // Some handlers failed
        http_response_code(202); // Accepted (partial success)

        // Log failures for investigation
        foreach ($result->getFailures() as $failure) {
            error_log("Webhook handler failed: " . $failure['error']->getMessage());
        }
    } else {
        // All handlers succeeded
        http_response_code(200);
        error_log("Webhook processed successfully: " . $event->getEventType());
    }

    // Return success response to LemonSqueezy
    echo json_encode(['success' => true]);

} catch (WebhookVerificationException $e) {
    // Signature verification failed - reject the webhook
    http_response_code(401);
    error_log("Webhook verification failed: " . $e->getMessage());

    echo json_encode(['error' => 'Signature verification failed']);
} catch (\Exception $e) {
    // Unexpected error
    http_response_code(500);
    error_log("Webhook processing error: " . $e->getMessage());

    echo json_encode(['error' => 'Internal server error']);
}

function register_webhook_listeners(): void {
    use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;

    // Register all your event listeners here
    EventDispatcher::register('order.created', function($event) {
        // Process order creation
    });

    EventDispatcher::register('subscription.updated', function($event) {
        // Process subscription update
    });

    // ... more listeners
}
PHP;

    echo $code . "\n";
}

// ============================================================================
// Example 6: Error handling with dispatch results
// ============================================================================

echo "\n=== Example 6: Error Handling ===\n";

EventDispatcher::clearAll();

EventDispatcher::register('order.created', function ($event) {
    echo "✓ Handler 1 succeeded\n";
});

EventDispatcher::register('order.created', function ($event) {
    echo "✗ Handler 2 will fail\n";
    throw new \Exception('Something went wrong in handler 2');
});

EventDispatcher::register('order.created', function ($event) {
    echo "✓ Handler 3 still runs despite handler 2 failure\n";
});

$testEvent = WebhookEvent::fromArray([
    'meta' => ['event_name' => 'order.created'],
    'data' => [],
]);

$dispatcher = new EventDispatcher();
$result = $dispatcher->dispatch($testEvent);

if ($result->hasFailures()) {
    echo "\nFailure details:\n";
    foreach ($result->getFailures() as $failure) {
        $error = $failure['error'];
        echo "  - Error: " . $error->getMessage() . "\n";
        echo "  - Code: " . $error->getCode() . "\n";
    }

    // Convert to array for logging/debugging
    $resultArray = $result->toArray();
    echo "\nResult as array:\n";
    echo json_encode($resultArray, JSON_PRETTY_PRINT) . "\n";
}

// ============================================================================
// Example 7: Processing different event types
// ============================================================================

echo "\n=== Example 7: Multiple Event Types ===\n";

EventDispatcher::clearAll();

// Order events
EventDispatcher::register('order.created', function ($event) {
    echo "Processing: order.created\n";
});

EventDispatcher::register('order.refunded', function ($event) {
    echo "Processing: order.refunded\n";
});

// Subscription events
EventDispatcher::register('subscription.created', function ($event) {
    echo "Processing: subscription.created\n";
});

EventDispatcher::register('subscription.updated', function ($event) {
    echo "Processing: subscription.updated\n";
});

EventDispatcher::register('subscription.expired', function ($event) {
    echo "Processing: subscription.expired\n";
});

EventDispatcher::register('subscription.cancelled', function ($event) {
    echo "Processing: subscription.cancelled\n";
});

// License key events
EventDispatcher::register('license-key.created', function ($event) {
    echo "Processing: license-key.created\n";
});

// Invoice events
EventDispatcher::register('subscription-invoice.paid', function ($event) {
    echo "Processing: subscription-invoice.paid\n";
});

// Process multiple events
$eventTypes = [
    'order.created',
    'subscription.updated',
    'license-key.created',
    'subscription-invoice.paid',
];

foreach ($eventTypes as $eventType) {
    $event = WebhookEvent::fromArray([
        'meta' => ['event_name' => $eventType],
        'data' => [],
    ]);

    $dispatcher = new EventDispatcher();
    $result = $dispatcher->dispatch($event);

    if ($result->hasFailures()) {
        echo "  ERROR: $eventType failed\n";
    }
}

// ============================================================================
// Example 8: Accessing event data and metadata
// ============================================================================

echo "\n=== Example 8: Event Data Access ===\n";

EventDispatcher::clearAll();

EventDispatcher::register('order.created', function ($event) {
    echo "Event information:\n";
    echo "  - Event type: " . $event->getEventType() . "\n";
    echo "  - Is verified: " . ($event->isVerified() ? 'Yes' : 'No') . "\n";

    $metadata = $event->getMetadata();
    echo "  - Received at: " . $metadata->getReceivedAt()->format('Y-m-d H:i:s') . "\n";
    echo "  - Algorithm: " . $metadata->getAlgorithm() . "\n";

    $webhookMeta = $event->getWebhookMeta();
    echo "  - Webhook ID: " . ($webhookMeta['webhook_id'] ?? 'N/A') . "\n";

    $rawData = $event->getRawData();
    if ($rawData) {
        echo "  - Data type: " . ($rawData['type'] ?? 'N/A') . "\n";
        echo "  - Data ID: " . ($rawData['id'] ?? 'N/A') . "\n";
    }
});

$eventWithMeta = WebhookEvent::fromArray([
    'meta' => [
        'event_name' => 'order.created',
        'webhook_id' => 'wh-abc123',
    ],
    'data' => [
        'type' => 'orders',
        'id' => 'ord-xyz789',
        'attributes' => ['total' => 99.99],
    ],
    'included' => [],
]);

$eventWithMeta->markVerified();

$dispatcher = new EventDispatcher();
$dispatcher->dispatch($eventWithMeta);

echo "\nWebhook listener examples completed!\n";
