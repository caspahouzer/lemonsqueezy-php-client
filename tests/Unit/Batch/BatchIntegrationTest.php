<?php

namespace LemonSqueezy\Tests\Unit\Batch;

use LemonSqueezy\Client;
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Batch\Operations\{
    BatchCreateOperation,
    BatchUpdateOperation,
    BatchDeleteOperation,
};
use LemonSqueezy\Tests\Unit\MockHttpClient;
use PHPUnit\Framework\TestCase;

class BatchIntegrationTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_key_123456789')
            ->withHttpClient(new MockHttpClient())
            ->build();

        try {
            $this->client = new Client($config);
        } catch (\Throwable $e) {
            $this->markTestSkipped('Guzzle or PSR factories not available: ' . $e->getMessage());
        }
    }

    public function testClientHasBatchMethod(): void
    {
        $this->assertTrue(method_exists($this->client, 'batch'));
    }

    public function testClientHasBatchCreateMethod(): void
    {
        $this->assertTrue(method_exists($this->client, 'batchCreate'));
    }

    public function testClientHasBatchUpdateMethod(): void
    {
        $this->assertTrue(method_exists($this->client, 'batchUpdate'));
    }

    public function testClientHasBatchDeleteMethod(): void
    {
        $this->assertTrue(method_exists($this->client, 'batchDelete'));
    }

    public function testBatchCreateWithValidOperations(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'Product 1']),
            new BatchCreateOperation('products', ['name' => 'Product 2']),
        ];

        $result = $this->client->batch($operations);

        $this->assertNotNull($result);
        $this->assertFalse($result->hasFailures());
        $this->assertEquals(2, $result->getTotalCount());
    }

    public function testBatchCreateConvenienceMethod(): void
    {
        $items = [
            ['name' => 'Product 1'],
            ['name' => 'Product 2'],
            ['name' => 'Product 3'],
        ];

        $result = $this->client->batchCreate('products', $items);

        $this->assertNotNull($result);
        $this->assertEquals(3, $result->getTotalCount());
    }

    public function testBatchUpdateConvenienceMethod(): void
    {
        $items = [
            ['id' => 'prod-1', 'name' => 'Updated 1'],
            ['id' => 'prod-2', 'name' => 'Updated 2'],
        ];

        $result = $this->client->batchUpdate('products', $items);

        $this->assertNotNull($result);
    }

    public function testBatchDeleteConvenienceMethod(): void
    {
        $ids = ['prod-1', 'prod-2', 'prod-3'];

        $result = $this->client->batchDelete('products', $ids);

        $this->assertNotNull($result);
    }

    public function testBatchMixedOperations(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'New Product']),
            new BatchUpdateOperation('products', 'prod-1', ['name' => 'Updated']),
            new BatchDeleteOperation('products', 'prod-2'),
        ];

        $result = $this->client->batch($operations);

        $this->assertNotNull($result);
        $this->assertEquals(3, $result->getTotalCount());
    }

    public function testBatchThrowsOnEmptyOperations(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Operations array cannot be empty');

        $this->client->batch([]);
    }

    public function testBatchThrowsOnInvalidOperations(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->client->batch([
            new BatchCreateOperation('products', ['name' => 'Product']),
            'not an operation',
        ]);
    }

    public function testBatchUpdateRequiresIdField(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each update item must contain an "id" key');

        $this->client->batchUpdate('products', [
            ['name' => 'Missing ID'],
        ]);
    }

    public function testBatchReturnsExecutionTime(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'Product 1']),
        ];

        $result = $this->client->batch($operations);

        $this->assertGreaterThanOrEqual(0, $result->getExecutionTime());
    }

    public function testBatchConfigOptions(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'Product 1']),
        ];

        $options = [
            'delayMs' => 100,
            'timeout' => 30,
            'stopOnError' => false,
        ];

        // Should not throw
        $result = $this->client->batch($operations, $options);

        $this->assertNotNull($result);
    }

    public function testBatchConfigInvalidDelayMs(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'Product 1']),
        ];

        $this->expectException(\InvalidArgumentException::class);

        $this->client->batch($operations, ['delayMs' => -100]);
    }

    public function testBatchConfigInvalidTimeout(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'Product 1']),
        ];

        $this->expectException(\InvalidArgumentException::class);

        $this->client->batch($operations, ['timeout' => 0]);
    }

    public function testBatchGetsSummaryStatistics(): void
    {
        $operations = [
            new BatchCreateOperation('products', ['name' => 'Product 1']),
            new BatchCreateOperation('products', ['name' => 'Product 2']),
        ];

        $result = $this->client->batch($operations);

        $summary = $result->getSummary();

        $this->assertArrayHasKey('totalRequested', $summary);
        $this->assertArrayHasKey('successCount', $summary);
        $this->assertArrayHasKey('failureCount', $summary);
        $this->assertArrayHasKey('successRate', $summary);
        $this->assertArrayHasKey('executionTime', $summary);
    }
}
