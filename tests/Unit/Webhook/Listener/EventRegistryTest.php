<?php

namespace LemonSqueezy\Tests\Unit\Webhook\Listener;

use LemonSqueezy\Webhook\Listener\EventRegistry;
use LemonSqueezy\Webhook\Listener\ListenerCollection;
use PHPUnit\Framework\TestCase;

class EventRegistryTest extends TestCase
{
    public function testRegisterListener(): void
    {
        $registry = new EventRegistry();
        $listener = fn() => null;

        $result = $registry->register('order.created', $listener);

        $this->assertSame($registry, $result);
        $this->assertTrue($registry->hasListeners('order.created'));
    }

    public function testGetListeners(): void
    {
        $registry = new EventRegistry();
        $listener1 = fn() => null;
        $listener2 = fn() => null;

        $registry->register('order.created', $listener1);
        $registry->register('order.created', $listener2);

        $listeners = $registry->getListeners('order.created');

        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(2, $listeners->count());
    }

    public function testGetNonExistentEvent(): void
    {
        $registry = new EventRegistry();
        $listeners = $registry->getListeners('non.existent');

        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertTrue($listeners->isEmpty());
    }

    public function testHasListeners(): void
    {
        $registry = new EventRegistry();
        $this->assertFalse($registry->hasListeners('order.created'));

        $registry->register('order.created', fn() => null);
        $this->assertTrue($registry->hasListeners('order.created'));
    }

    public function testGetEventTypes(): void
    {
        $registry = new EventRegistry();

        $registry->register('order.created', fn() => null);
        $registry->register('subscription.updated', fn() => null);

        $types = $registry->getEventTypes();

        $this->assertContains('order.created', $types);
        $this->assertContains('subscription.updated', $types);
        $this->assertEquals(2, count($types));
    }

    public function testClearAll(): void
    {
        $registry = new EventRegistry();
        $registry->register('order.created', fn() => null);
        $registry->register('subscription.updated', fn() => null);

        $this->assertEquals(2, count($registry->getEventTypes()));

        $result = $registry->clear();

        $this->assertSame($registry, $result);
        $this->assertEquals(0, count($registry->getEventTypes()));
    }

    public function testClearEvent(): void
    {
        $registry = new EventRegistry();
        $registry->register('order.created', fn() => null);
        $registry->register('subscription.updated', fn() => null);

        $registry->clearEvent('order.created');

        $this->assertFalse($registry->hasListeners('order.created'));
        $this->assertTrue($registry->hasListeners('subscription.updated'));
    }

    public function testCountListeners(): void
    {
        $registry = new EventRegistry();
        $registry->register('order.created', fn() => null);
        $registry->register('order.created', fn() => null);
        $registry->register('subscription.updated', fn() => null);

        $this->assertEquals(2, $registry->countListeners('order.created'));
        $this->assertEquals(1, $registry->countListeners('subscription.updated'));
        $this->assertEquals(0, $registry->countListeners('non.existent'));
    }

    public function testCountAll(): void
    {
        $registry = new EventRegistry();
        $registry->register('order.created', fn() => null);
        $registry->register('order.created', fn() => null);
        $registry->register('subscription.updated', fn() => null);

        $this->assertEquals(3, $registry->countAll());
    }

    public function testGetAll(): void
    {
        $registry = new EventRegistry();
        $registry->register('order.created', fn() => null);
        $registry->register('subscription.updated', fn() => null);

        $all = $registry->all();

        $this->assertEquals(2, count($all));
        $this->assertArrayHasKey('order.created', $all);
        $this->assertArrayHasKey('subscription.updated', $all);
    }

    public function testFluentInterface(): void
    {
        $registry = new EventRegistry();

        $result = $registry
            ->register('order.created', fn() => null)
            ->register('order.refunded', fn() => null)
            ->register('subscription.updated', fn() => null);

        $this->assertSame($registry, $result);
        $this->assertEquals(3, count($registry->getEventTypes()));
    }
}
