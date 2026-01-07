<?php

namespace LemonSqueezy\Tests\Unit\Webhook\Dispatcher;

use LemonSqueezy\Webhook\Dispatcher\EventDispatcher;
use LemonSqueezy\Webhook\Event\WebhookEvent;
use LemonSqueezy\Webhook\Listener\EventListenerInterface;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    private WebhookEvent $event;

    protected function setUp(): void
    {
        EventDispatcher::clearAll();

        $payload = [
            'meta' => ['event_name' => 'order.created'],
            'data' => ['type' => 'orders', 'id' => '123'],
        ];

        $this->event = WebhookEvent::fromArray($payload);
    }

    public function testRegisterAndDispatchWithClosure(): void
    {
        $called = false;

        EventDispatcher::register('order.created', function ($event) use (&$called) {
            $called = true;
        });

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($this->event);

        $this->assertTrue($called);
        $this->assertTrue($result->allSucceeded());
        $this->assertEquals(1, $result->getHandlerCount());
    }

    public function testDispatchWithMultipleListeners(): void
    {
        $calls = [];

        EventDispatcher::register('order.created', function () use (&$calls) {
            $calls[] = 'handler1';
        });

        EventDispatcher::register('order.created', function () use (&$calls) {
            $calls[] = 'handler2';
        });

        $dispatcher = new EventDispatcher();
        $dispatcher->dispatch($this->event);

        $this->assertEquals(['handler1', 'handler2'], $calls);
    }

    public function testDispatchWithListenerClass(): void
    {
        $listener = new class () implements EventListenerInterface {
            public $handled = false;

            public function handle(\LemonSqueezy\Webhook\Event\EventInterface $event): void
            {
                $this->handled = true;
            }
        };

        EventDispatcher::register('order.created', $listener);

        $dispatcher = new EventDispatcher();
        $dispatcher->dispatch($this->event);

        $this->assertTrue($listener->handled);
    }

    public function testDispatchRecordsFailures(): void
    {
        EventDispatcher::register('order.created', function () {
            throw new \Exception('Handler error');
        });

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($this->event);

        $this->assertTrue($result->hasFailures());
        $this->assertEquals(1, $result->getFailureCount());
        $this->assertEquals(0, $result->getSuccessCount());
    }

    public function testDispatchContinuesAfterFailure(): void
    {
        $calls = [];

        EventDispatcher::register('order.created', function () use (&$calls) {
            throw new \Exception('Error');
        });

        EventDispatcher::register('order.created', function () use (&$calls) {
            $calls[] = 'second_handler';
        });

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($this->event);

        // Second handler should still be called despite first handler failure
        $this->assertEquals(['second_handler'], $calls);
        $this->assertEquals(1, $result->getFailureCount());
        $this->assertEquals(1, $result->getSuccessCount());
    }

    public function testHasListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $this->assertFalse($dispatcher->hasListeners('order.created'));

        EventDispatcher::register('order.created', function () {
        });

        $this->assertTrue($dispatcher->hasListeners('order.created'));
    }

    public function testUnregisterEvent(): void
    {
        EventDispatcher::register('order.created', function () {
        });

        $dispatcher = new EventDispatcher();
        $this->assertTrue($dispatcher->hasListeners('order.created'));

        EventDispatcher::unregister('order.created');

        $this->assertFalse($dispatcher->hasListeners('order.created'));
    }

    public function testGetDispatchResult(): void
    {
        EventDispatcher::register('order.created', function () {
        });

        $dispatcher = new EventDispatcher();
        $result = $dispatcher->dispatch($this->event);

        $this->assertEquals('order.created', $result->getEventType());
        $this->assertEquals(1, $result->getHandlerCount());
        $this->assertTrue($result->allSucceeded());
    }

    public function testInstanceIsolation(): void
    {
        $dispatcher1 = new EventDispatcher();
        $dispatcher2 = new EventDispatcher();

        // Both should use the global registry by default
        EventDispatcher::register('order.created', function () {
        });

        $this->assertTrue($dispatcher1->hasListeners('order.created'));
        $this->assertTrue($dispatcher2->hasListeners('order.created'));
    }
}
