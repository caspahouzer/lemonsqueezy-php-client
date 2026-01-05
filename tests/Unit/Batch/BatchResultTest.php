<?php

namespace LemonSqueezy\Tests\Unit\Batch;

use LemonSqueezy\Batch\BatchResult;
use LemonSqueezy\Batch\Operations\BatchCreateOperation;
use LemonSqueezy\Batch\Operations\BatchUpdateOperation;
use LemonSqueezy\Batch\Operations\BatchDeleteOperation;
use PHPUnit\Framework\TestCase;

class BatchResultTest extends TestCase
{
    private BatchResult $result;

    protected function setUp(): void
    {
        $this->result = new BatchResult();
    }

    public function testCanAddSuccessfulOperation(): void
    {
        $operation = new BatchCreateOperation('products', ['name' => 'Test Product']);
        $data = ['id' => 'prod-123', 'name' => 'Test Product'];

        $this->result->addSuccess($operation, $data, 201);

        $this->assertEquals(1, $this->result->getSuccessCount());
        $this->assertEquals(0, $this->result->getFailureCount());
        $this->assertTrue($this->result->wasSuccessful());
    }

    public function testCanAddFailedOperation(): void
    {
        $operation = new BatchCreateOperation('products', ['name' => 'Test Product']);

        $this->result->addFailure($operation, 'Invalid data', 400);

        $this->assertEquals(0, $this->result->getSuccessCount());
        $this->assertEquals(1, $this->result->getFailureCount());
        $this->assertFalse($this->result->wasSuccessful());
        $this->assertTrue($this->result->hasFailures());
    }

    public function testCanTrackMixedResults(): void
    {
        $createOp = new BatchCreateOperation('products', ['name' => 'Product 1']);
        $updateOp = new BatchUpdateOperation('products', 'prod-1', ['name' => 'Updated']);
        $deleteOp = new BatchDeleteOperation('products', 'prod-2');

        $this->result->addSuccess($createOp, ['id' => 'prod-1'], 201);
        $this->result->addSuccess($updateOp, ['id' => 'prod-1'], 200);
        $this->result->addFailure($deleteOp, 'Not found', 404);

        $this->assertEquals(2, $this->result->getSuccessCount());
        $this->assertEquals(1, $this->result->getFailureCount());
        $this->assertEquals(3, $this->result->getTotalCount());
        $this->assertFalse($this->result->wasSuccessful());
    }

    public function testReturnSuccessfulOperations(): void
    {
        $operation = new BatchCreateOperation('products', ['name' => 'Test']);
        $data = ['id' => 'prod-123', 'name' => 'Test'];

        $this->result->addSuccess($operation, $data, 201);

        $successful = $this->result->getSuccessful();
        $this->assertCount(1, $successful);
        $this->assertEquals('Test', $successful[0]['result']['name']);
        $this->assertEquals(201, $successful[0]['statusCode']);
    }

    public function testReturnFailedOperations(): void
    {
        $operation = new BatchCreateOperation('products', ['name' => 'Test']);

        $this->result->addFailure($operation, 'Invalid name', 400, ['field' => 'name']);

        $failed = $this->result->getFailed();
        $this->assertCount(1, $failed);
        $this->assertEquals('Invalid name', $failed[0]['error']);
        $this->assertEquals(400, $failed[0]['statusCode']);
        $this->assertEquals(['field' => 'name'], $failed[0]['errorDetails']);
    }

    public function testGetSummaryStatistics(): void
    {
        $this->result->addSuccess(new BatchCreateOperation('products', ['name' => 'P1']), [], 201);
        $this->result->addSuccess(new BatchCreateOperation('products', ['name' => 'P2']), [], 201);
        $this->result->addFailure(new BatchCreateOperation('products', ['name' => 'P3']), 'Error', 400);
        $this->result->setExecutionTime(1.5);

        $summary = $this->result->getSummary();

        $this->assertEquals(3, $summary['totalRequested']);
        $this->assertEquals(2, $summary['successCount']);
        $this->assertEquals(1, $summary['failureCount']);
        $this->assertEquals(66.67, $summary['successRate']);
        $this->assertEquals(1.5, $summary['executionTime']);
    }

    public function testFluentInterface(): void
    {
        $op1 = new BatchCreateOperation('products', ['name' => 'P1']);
        $op2 = new BatchCreateOperation('products', ['name' => 'P2']);

        $chainedResult = $this->result
            ->addSuccess($op1, [], 201)
            ->addSuccess($op2, [], 201)
            ->setExecutionTime(0.5);

        $this->assertInstanceOf(BatchResult::class, $chainedResult);
        $this->assertEquals(2, $this->result->getSuccessCount());
        $this->assertEquals(0.5, $this->result->getExecutionTime());
    }

    public function testEmptyBatchIsSuccessful(): void
    {
        $this->assertTrue($this->result->wasSuccessful());
        $this->assertFalse($this->result->hasFailures());
        $this->assertEquals(0, $this->result->getTotalCount());
    }
}
