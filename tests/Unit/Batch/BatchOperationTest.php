<?php

namespace LemonSqueezy\Tests\Unit\Batch;

use LemonSqueezy\Batch\Operations\{
    BatchCreateOperation,
    BatchUpdateOperation,
    BatchDeleteOperation,
};
use PHPUnit\Framework\TestCase;

class BatchOperationTest extends TestCase
{
    public function testBatchCreateOperation(): void
    {
        $data = ['name' => 'Test Product', 'price' => 29.99];
        $operation = new BatchCreateOperation('products', $data);

        $this->assertEquals('products', $operation->getResource());
        $this->assertEquals('create', $operation->getOperationType());
        $this->assertEquals('POST', $operation->getMethod());
        $this->assertEquals('products', $operation->getEndpoint());
        $this->assertEquals(['data' => $data], $operation->getPayload());
        $this->assertEquals($data, $operation->getData());
    }

    public function testBatchUpdateOperation(): void
    {
        $data = ['name' => 'Updated Name'];
        $operation = new BatchUpdateOperation('products', 'prod-123', $data);

        $this->assertEquals('products', $operation->getResource());
        $this->assertEquals('update', $operation->getOperationType());
        $this->assertEquals('PATCH', $operation->getMethod());
        $this->assertEquals('products/prod-123', $operation->getEndpoint());
        $this->assertEquals('prod-123', $operation->getId());
        $this->assertEquals($data, $operation->getData());

        // Payload should merge ID with data
        $payload = $operation->getPayload();
        $this->assertEquals('prod-123', $payload['data']['id']);
        $this->assertEquals('Updated Name', $payload['data']['name']);
    }

    public function testBatchDeleteOperation(): void
    {
        $operation = new BatchDeleteOperation('products', 'prod-456');

        $this->assertEquals('products', $operation->getResource());
        $this->assertEquals('delete', $operation->getOperationType());
        $this->assertEquals('DELETE', $operation->getMethod());
        $this->assertEquals('products/prod-456', $operation->getEndpoint());
        $this->assertEquals('prod-456', $operation->getId());
        $this->assertNull($operation->getPayload());
    }

    public function testUpdateOperationEncodesIdInEndpoint(): void
    {
        $operation = new BatchUpdateOperation('products', 'prod-123/special', []);

        // Should URL encode the ID
        $this->assertStringContainsString('prod-123%2Fspecial', $operation->getEndpoint());
    }

    public function testDeleteOperationEncodesIdInEndpoint(): void
    {
        $operation = new BatchDeleteOperation('products', 'prod-123/special');

        // Should URL encode the ID
        $this->assertStringContainsString('prod-123%2Fspecial', $operation->getEndpoint());
    }

    public function testCreateOperationWithComplexData(): void
    {
        $data = [
            'name' => 'Product',
            'description' => 'A test product',
            'prices' => [100, 200, 300],
            'metadata' => ['color' => 'red', 'size' => 'large'],
        ];

        $operation = new BatchCreateOperation('products', $data);
        $payload = $operation->getPayload();

        $this->assertEquals($data, $payload['data']);
    }

    public function testUpdateOperationPreservesAllData(): void
    {
        $updateData = ['status' => 'inactive', 'tags' => ['discontinued']];
        $operation = new BatchUpdateOperation('products', 'prod-123', $updateData);

        $payload = $operation->getPayload();

        $this->assertEquals('prod-123', $payload['data']['id']);
        $this->assertEquals('inactive', $payload['data']['status']);
        $this->assertEquals(['discontinued'], $payload['data']['tags']);
    }
}
