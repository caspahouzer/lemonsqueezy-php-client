<?php

namespace LemonSqueezy\Tests\Unit\Webhook\Listener;

use LemonSqueezy\Webhook\Listener\ListenerCollection;
use PHPUnit\Framework\TestCase;

class ListenerCollectionTest extends TestCase
{
    public function testAddListener(): void
    {
        $collection = new ListenerCollection();
        $listener = fn() => null;

        $result = $collection->add($listener);

        $this->assertSame($collection, $result);
        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->isEmpty());
    }

    public function testGetAllListeners(): void
    {
        $collection = new ListenerCollection();
        $listener1 = fn() => null;
        $listener2 = fn() => null;

        $collection->add($listener1)->add($listener2);

        $listeners = $collection->all();
        $this->assertEquals([$listener1, $listener2], $listeners);
    }

    public function testEmptyCollection(): void
    {
        $collection = new ListenerCollection();

        $this->assertTrue($collection->isEmpty());
        $this->assertEquals(0, $collection->count());
    }

    public function testClearCollection(): void
    {
        $collection = new ListenerCollection();
        $collection->add(fn() => null)->add(fn() => null);

        $result = $collection->clear();

        $this->assertSame($collection, $result);
        $this->assertTrue($collection->isEmpty());
        $this->assertEquals(0, $collection->count());
    }

    public function testIterator(): void
    {
        $collection = new ListenerCollection();
        $listener1 = fn() => null;
        $listener2 = fn() => null;

        $collection->add($listener1)->add($listener2);

        $listeners = [];
        foreach ($collection->getIterator() as $listener) {
            $listeners[] = $listener;
        }

        $this->assertEquals([$listener1, $listener2], $listeners);
    }

    public function testFluentInterface(): void
    {
        $collection = new ListenerCollection();

        $result = $collection
            ->add(fn() => null)
            ->add(fn() => null)
            ->add(fn() => null);

        $this->assertSame($collection, $result);
        $this->assertEquals(3, $collection->count());
    }
}
