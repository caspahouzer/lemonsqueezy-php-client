<?php

namespace LemonSqueezy\Tests\Unit\Webhook\Dispatcher;

use LemonSqueezy\Webhook\Dispatcher\DispatchResult;
use PHPUnit\Framework\TestCase;

class DispatchResultTest extends TestCase
{
    public function testCreateDispatchResult(): void
    {
        $result = new DispatchResult('order.created', 2);

        $this->assertEqual('order.created', $result->getEventType());
        $this->assertEqual(2, $result->getHandlerCount());
        $this->assertFalse($result->hasFailures());
        $this->assertEqual(0, $result->getFailureCount());
    }

    public function testRecordSuccess(): void
    {
        $result = new DispatchResult('order.created');
        $handler = fn () => null;

        $result->recordSuccess($handler, 'result');

        $this->assertEqual(1, $result->getSuccessCount());
        $this->assertEqual(['handler' => $handler, 'return_value' => 'result'], $result->getSuccesses()[0]);
    }

    public function testRecordFailure(): void
    {
        $result = new DispatchResult('order.created');
        $handler = fn () => null;
        $error = new \Exception('Test error');

        $result->recordFailure($handler, $error);

        $this->assertEqual(1, $result->getFailureCount());
        $this->assertTrue($result->hasFailures());
        $this->assertEqual($handler, $result->getFailures()[0]['handler']);
        $this->assertSame($error, $result->getFailures()[0]['error']);
    }

    public function testAllSucceeded(): void
    {
        $result = new DispatchResult('order.created', 1);
        $result->recordSuccess(fn () => null);

        $this->assertTrue($result->allSucceeded());
    }

    public function testAllSucceededWithFailures(): void
    {
        $result = new DispatchResult('order.created', 2);
        $result->recordSuccess(fn () => null);
        $result->recordFailure(fn () => null, new \Exception('error'));

        $this->assertFalse($result->allSucceeded());
    }

    public function testAllSucceededNoHandlers(): void
    {
        $result = new DispatchResult('order.created', 0);

        $this->assertFalse($result->allSucceeded());
    }

    public function testConvertToArray(): void
    {
        $result = new DispatchResult('order.created', 2);
        $result->recordSuccess(fn () => null);
        $result->recordFailure(fn () => null, new \Exception('test error'));

        $array = $result->toArray();

        $this->assertEqual('order.created', $array['event_type']);
        $this->assertEqual(2, $array['handler_count']);
        $this->assertEqual(1, $array['success_count']);
        $this->assertEqual(1, $array['failure_count']);
        $this->assertFalse($array['all_succeeded']);
        $this->assertIsArray($array['failures']);
        $this->assertEqual(1, count($array['failures']));
    }

    public function testFluentInterface(): void
    {
        $handler = fn () => null;
        $result = new DispatchResult('order.created')
            ->recordSuccess($handler)
            ->recordSuccess($handler)
            ->recordFailure($handler, new \Exception('error'));

        $this->assertEqual(2, $result->getSuccessCount());
        $this->assertEqual(1, $result->getFailureCount());
    }

    public function testSerializeClosureHandler(): void
    {
        $result = new DispatchResult('order.created');
        $closure = fn () => null;
        $result->recordFailure($closure, new \Exception('error'));

        $array = $result->toArray();
        $this->assertEqual('Closure', $array['failures'][0]['handler']);
    }

    public function testSerializeObjectHandler(): void
    {
        $result = new DispatchResult('order.created');
        $object = new class {
        };
        $result->recordFailure($object, new \Exception('error'));

        $array = $result->toArray();
        $this->assertStringContainsString('stdClass', $array['failures'][0]['handler']);
    }

    public function testSerializeStringHandler(): void
    {
        $result = new DispatchResult('order.created');
        $result->recordFailure('my_function', new \Exception('error'));

        $array = $result->toArray();
        $this->assertEqual('my_function', $array['failures'][0]['handler']);
    }
}
