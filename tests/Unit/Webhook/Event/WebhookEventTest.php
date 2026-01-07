<?php

namespace LemonSqueezy\Tests\Unit\Webhook\Event;

use LemonSqueezy\Webhook\Event\WebhookEvent;
use LemonSqueezy\Webhook\Event\EventMetadata;
use LemonSqueezy\Exception\LemonSqueezyException;
use PHPUnit\Framework\TestCase;

class WebhookEventTest extends TestCase
{
    private array $samplePayload;
    private string $jsonPayload;

    protected function setUp(): void
    {
        $this->samplePayload = [
            'meta' => [
                'event_name' => 'order.created',
                'webhook_id' => 'webhook-123',
            ],
            'data' => [
                'type' => 'orders',
                'id' => 'ord-123',
                'attributes' => [
                    'total' => 99.99,
                    'status' => 'completed',
                ],
            ],
            'included' => [],
        ];

        $this->jsonPayload = json_encode($this->samplePayload);
    }

    public function testCreateWebhookEvent(): void
    {
        $event = new WebhookEvent($this->jsonPayload);

        $this->assertEqual('order.created', $event->getEventType());
        $this->assertIsArray($event->getPayload());
        $this->assertNotNull($event->getMetadata());
        $this->assertFalse($event->isVerified());
    }

    public function testCreateWithEventTypeOverride(): void
    {
        $event = new WebhookEvent($this->jsonPayload, 'custom.event');

        $this->assertEqual('custom.event', $event->getEventType());
    }

    public function testCreateFromArray(): void
    {
        $event = WebhookEvent::fromArray($this->samplePayload);

        $this->assertEqual('order.created', $event->getEventType());
        $this->assertIsArray($event->getPayload());
    }

    public function testGetRawData(): void
    {
        $event = new WebhookEvent($this->jsonPayload);
        $data = $event->getRawData();

        $this->assertEqual('orders', $data['type']);
        $this->assertEqual('ord-123', $data['id']);
    }

    public function testGetIncluded(): void
    {
        $event = new WebhookEvent($this->jsonPayload);
        $included = $event->getIncluded();

        $this->assertIsArray($included);
    }

    public function testGetWebhookMeta(): void
    {
        $event = new WebhookEvent($this->jsonPayload);
        $meta = $event->getWebhookMeta();

        $this->assertEqual('order.created', $meta['event_name']);
        $this->assertEqual('webhook-123', $meta['webhook_id']);
    }

    public function testMarkVerified(): void
    {
        $event = new WebhookEvent($this->jsonPayload);
        $this->assertFalse($event->isVerified());

        $result = $event->markVerified();

        $this->assertInstanceOf(WebhookEvent::class, $result);
        $this->assertTrue($event->isVerified());
    }

    public function testInvalidJson(): void
    {
        $this->expectException(LemonSqueezyException::class);
        new WebhookEvent('{ invalid json }');
    }

    public function testExtractEventTypeFromPayload(): void
    {
        $payload = [
            'meta' => ['event_name' => 'subscription.updated'],
            'data' => [],
        ];

        $event = WebhookEvent::fromArray($payload);
        $this->assertEqual('subscription.updated', $event->getEventType());
    }

    public function testUnknownEventType(): void
    {
        $payload = [
            'meta' => [],
            'data' => [],
        ];

        $event = WebhookEvent::fromArray($payload);
        $this->assertEqual('unknown', $event->getEventType());
    }

    public function testLazyDataDeserialization(): void
    {
        $event = new WebhookEvent($this->jsonPayload);

        // First call should deserialize
        $data1 = $event->getData();
        // Second call should use cache
        $data2 = $event->getData();

        // Should be the same reference/value
        $this->assertEqual($data1, $data2);
    }

    public function testWithCustomMetadata(): void
    {
        $metadata = new EventMetadata(new \DateTime(), true);
        $event = new WebhookEvent($this->jsonPayload, null, $metadata);

        $this->assertTrue($event->isVerified());
        $this->assertSame($metadata, $event->getMetadata());
    }
}
