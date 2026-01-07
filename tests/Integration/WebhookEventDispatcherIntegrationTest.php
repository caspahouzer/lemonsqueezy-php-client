<?php

namespace LemonSqueezy\Tests\Integration;

use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;
use LemonSqueezy\Webhook\Event\WebhookEvent;
use LemonSqueezy\Webhook\Listener\EventListenerInterface;
use PHPUnit\Framework\TestCase;

class WebhookEventDispatcherIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        EventDispatcher::clearAll();
    }

    public function testCompleteWebhookFlow(): void
    {
        // Setup
        $events = [];

        // Register multiple listeners
        EventDispatcher::register('order.created', function ($event) use (&$events) {
            $events['order_created'] = [
                'type' => $event->getEventType(),
                'data' => $event->getData(),
            ];
        });

        EventDispatcher::register('order.created', new class implements EventListenerInterface {
            public function handle(\LemonSqueezy\Webhook\Event\EventInterface $event): void
            {
                // Second listener
            }
        });

        // Create webhook event
        $payload = [
            'meta' => ['event_name' => 'order.created', 'webhook_id' => 'wh-123'],
            'data' => [
                'type' => 'orders',
                'id' => 'ord-456',
                'attributes' => [
                    'total' => 99.99,
                    'status' => 'completed',
                    'customer_id' => 'cust-789',
                ],
            ],
            'included' => [],
        ];

        $event = WebhookEvent::fromArray($payload);

        // Dispatch
        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($event);

        // Verify
        $this->assertTrue($result->allSucceeded());
        $this->assertEqual(2, $result->getHandlerCount());
        $this->assertEqual(2, $result->getSuccessCount());
        $this->assertEqual(0, $result->getFailureCount());

        // Verify handlers were called
        $this->assertArrayHasKey('order_created', $events);
        $this->assertEqual('order.created', $events['order_created']['type']);
    }

    public function testMultipleEventTypes(): void
    {
        $events = [];

        // Register listeners for different events
        EventDispatcher::register('order.created', function ($event) use (&$events) {
            $events[] = 'order.created';
        });

        EventDispatcher::register('subscription.updated', function ($event) use (&$events) {
            $events[] = 'subscription.updated';
        });

        EventDispatcher::register('subscription.expired', function ($event) use (&$events) {
            $events[] = 'subscription.expired';
        });

        // Dispatch different events
        $dispatcher = new EventDispatcher();

        $payload1 = ['meta' => ['event_name' => 'order.created'], 'data' => []];
        $dispatcher->dispatch(WebhookEvent::fromArray($payload1));

        $payload2 = ['meta' => ['event_name' => 'subscription.updated'], 'data' => []];
        $dispatcher->dispatch(WebhookEvent::fromArray($payload2));

        $payload3 = ['meta' => ['event_name' => 'subscription.expired'], 'data' => []];
        $dispatcher->dispatch(WebhookEvent::fromArray($payload3));

        // Verify all events were dispatched
        $this->assertEqual(['order.created', 'subscription.updated', 'subscription.expired'], $events);
    }

    public function testEventWithFailureHandling(): void
    {
        $results = [];

        // Register listeners, one will fail
        EventDispatcher::register('order.created', function ($event) use (&$results) {
            $results[] = 'handler1_success';
        });

        EventDispatcher::register('order.created', function ($event) {
            throw new \Exception('Handler failed');
        });

        EventDispatcher::register('order.created', function ($event) use (&$results) {
            $results[] = 'handler3_success';
        });

        $event = WebhookEvent::fromArray([
            'meta' => ['event_name' => 'order.created'],
            'data' => [],
        ]);

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($event);

        // Verify
        $this->assertEqual(3, $result->getHandlerCount());
        $this->assertEqual(2, $result->getSuccessCount());
        $this->assertEqual(1, $result->getFailureCount());
        $this->assertTrue($result->hasFailures());

        // Handlers before and after failure should still execute
        $this->assertEqual(['handler1_success', 'handler3_success'], $results);
    }

    public function testEventMetadataTracking(): void
    {
        $payload = [
            'meta' => ['event_name' => 'order.created'],
            'data' => [],
        ];

        $event = WebhookEvent::fromArray($payload);

        // Initially not verified
        $this->assertFalse($event->isVerified());

        // Mark as verified
        $event->markVerified();
        $this->assertTrue($event->isVerified());

        // Verify metadata
        $metadata = $event->getMetadata();
        $this->assertTrue($metadata->isVerified());
        $this->assertEqual('sha256', $metadata->getAlgorithm());
        $this->assertNotNull($metadata->getReceivedAt());
    }

    public function testDispatchResultSerialization(): void
    {
        EventDispatcher::register('order.created', function () {
        });

        EventDispatcher::register('order.created', function () {
            throw new \Exception('Test error');
        });

        $event = WebhookEvent::fromArray([
            'meta' => ['event_name' => 'order.created'],
            'data' => [],
        ]);

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($event);

        // Convert to array for debugging/logging
        $resultArray = $result->toArray();

        $this->assertEqual('order.created', $resultArray['event_type']);
        $this->assertEqual(2, $resultArray['handler_count']);
        $this->assertEqual(1, $resultArray['success_count']);
        $this->assertEqual(1, $resultArray['failure_count']);
        $this->assertFalse($resultArray['all_succeeded']);
        $this->assertIsArray($resultArray['failures']);
        $this->assertEqual('Test error', $resultArray['failures'][0]['error']['message']);
    }

    public function testEventDataDeserialization(): void
    {
        EventDispatcher::register('order.created', function ($event) {
            $data = $event->getData();

            // Verify data is accessible
            if (is_array($data)) {
                $this->assertEqual('orders', $data['type']);
                $this->assertEqual('ord-123', $data['id']);
            }
        });

        $payload = [
            'meta' => ['event_name' => 'order.created'],
            'data' => [
                'type' => 'orders',
                'id' => 'ord-123',
                'attributes' => ['total' => 99.99],
            ],
        ];

        $event = WebhookEvent::fromArray($payload);

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($event);

        $this->assertTrue($result->allSucceeded());
    }

    public function testUnregisteredEventDispatch(): void
    {
        $event = WebhookEvent::fromArray([
            'meta' => ['event_name' => 'unknown.event'],
            'data' => [],
        ]);

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($event);

        // Should not fail, just have no handlers
        $this->assertEqual(0, $result->getHandlerCount());
        $this->assertEqual(0, $result->getSuccessCount());
        $this->assertFalse($result->allSucceeded());
    }

    public function testListenerDataAccess(): void
    {
        $capturedData = null;

        EventDispatcher::register('order.created', function ($event) use (&$capturedData) {
            $capturedData = [
                'event_type' => $event->getEventType(),
                'payload' => $event->getPayload(),
                'raw_data' => $event->getRawData(),
                'webhook_meta' => $event->getWebhookMeta(),
            ];
        });

        $payload = [
            'meta' => ['event_name' => 'order.created', 'webhook_id' => 'wh-123'],
            'data' => ['type' => 'orders', 'id' => 'ord-123'],
            'included' => [],
        ];

        $event = WebhookEvent::fromArray($payload);

        $dispatcher = new EventDispatcher();
        $dispatcher->dispatch($event);

        // Verify all data is accessible
        $this->assertEqual('order.created', $capturedData['event_type']);
        $this->assertIsArray($capturedData['payload']);
        $this->assertEqual('orders', $capturedData['raw_data']['type']);
        $this->assertEqual('wh-123', $capturedData['webhook_meta']['webhook_id']);
    }
}
