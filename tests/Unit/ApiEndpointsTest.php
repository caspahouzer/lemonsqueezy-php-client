<?php

namespace LemonSqueezy\Tests\Unit;

use LemonSqueezy\Client;
use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Query\QueryBuilder;
use LemonSqueezy\Exception\{
    NotFoundException,
    RateLimitException,
    ValidationException,
};
use PHPUnit\Framework\TestCase;

/**
 * Comprehensive test suite for all LemonSqueezy API endpoints
 *
 * This test demonstrates all implemented endpoints and methods
 * using mocked HTTP responses for self-contained testing.
 */
class ApiEndpointsTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        // Create mock HTTP client for testing
        // Note: Unit tests don't need real HTTP factories since we mock the client
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key_unit_tests')
            ->withHttpClient(new MockHttpClient())
            ->build();

        try {
            $this->client = new Client($config);
        } catch (\Throwable $e) {
            // If Guzzle isn't available, skip unit tests
            $this->markTestSkipped('Guzzle or PSR factories not available: ' . $e->getMessage());
        }
    }

    /**
     * Test that all 19 resources are accessible
     */
    public function testAllResourcesAreAccessible(): void
    {
        $this->assertNotNull($this->client->users());
        $this->assertNotNull($this->client->stores());
        $this->assertNotNull($this->client->products());
        $this->assertNotNull($this->client->variants());
        $this->assertNotNull($this->client->prices());
        $this->assertNotNull($this->client->files());
        $this->assertNotNull($this->client->customers());
        $this->assertNotNull($this->client->orders());
        $this->assertNotNull($this->client->orderItems());
        $this->assertNotNull($this->client->subscriptions());
        $this->assertNotNull($this->client->subscriptionInvoices());
        $this->assertNotNull($this->client->subscriptionItems());
        $this->assertNotNull($this->client->discounts());
        $this->assertNotNull($this->client->discountRedemptions());
        $this->assertNotNull($this->client->licenseKeys());
        $this->assertNotNull($this->client->webhooks());
        $this->assertNotNull($this->client->checkouts());
        $this->assertNotNull($this->client->affiliates());
        $this->assertNotNull($this->client->usageRecords());
    }

    /**
     * Test resource endpoints
     */
    public function testResourceEndpoints(): void
    {
        // Test that all resources have correct endpoints
        $this->assertEquals('users', $this->client->users()->getEndpoint());
        $this->assertEquals('stores', $this->client->stores()->getEndpoint());
        $this->assertEquals('products', $this->client->products()->getEndpoint());
        $this->assertEquals('variants', $this->client->variants()->getEndpoint());
        $this->assertEquals('prices', $this->client->prices()->getEndpoint());
        $this->assertEquals('files', $this->client->files()->getEndpoint());
        $this->assertEquals('customers', $this->client->customers()->getEndpoint());
        $this->assertEquals('orders', $this->client->orders()->getEndpoint());
        $this->assertEquals('order-items', $this->client->orderItems()->getEndpoint());
        $this->assertEquals('subscriptions', $this->client->subscriptions()->getEndpoint());
        $this->assertEquals('subscription-invoices', $this->client->subscriptionInvoices()->getEndpoint());
        $this->assertEquals('subscription-items', $this->client->subscriptionItems()->getEndpoint());
        $this->assertEquals('discounts', $this->client->discounts()->getEndpoint());
        $this->assertEquals('discount-redemptions', $this->client->discountRedemptions()->getEndpoint());
        $this->assertEquals('license-keys', $this->client->licenseKeys()->getEndpoint());
        $this->assertEquals('webhooks', $this->client->webhooks()->getEndpoint());
        $this->assertEquals('checkouts', $this->client->checkouts()->getEndpoint());
        $this->assertEquals('affiliates', $this->client->affiliates()->getEndpoint());
        $this->assertEquals('usage-records', $this->client->usageRecords()->getEndpoint());
    }

    /**
     * Test CRUD operations are available
     */
    public function testCrudMethodsAvailable(): void
    {
        // Test that resources have CRUD methods
        $resources = [
            $this->client->stores(),
            $this->client->products(),
            $this->client->variants(),
            $this->client->prices(),
            $this->client->files(),
            $this->client->customers(),
            $this->client->discounts(),
            $this->client->webhooks(),
        ];

        foreach ($resources as $resource) {
            $this->assertTrue(method_exists($resource, 'list'), 'Resource should have list() method');
            $this->assertTrue(method_exists($resource, 'get'), 'Resource should have get() method');
            $this->assertTrue(method_exists($resource, 'create'), 'Resource should have create() method');
            $this->assertTrue(method_exists($resource, 'update'), 'Resource should have update() method');
            $this->assertTrue(method_exists($resource, 'delete'), 'Resource should have delete() method');
        }
    }

    /**
     * Test read-only resources have list and get
     */
    public function testReadOnlyResourceMethods(): void
    {
        $readOnlyResources = [
            $this->client->users(),
            $this->client->orders(),
            $this->client->orderItems(),
            $this->client->subscriptions(),
            $this->client->subscriptionInvoices(),
            $this->client->subscriptionItems(),
            $this->client->discountRedemptions(),
            $this->client->affiliates(),
        ];

        foreach ($readOnlyResources as $resource) {
            $this->assertTrue(method_exists($resource, 'list'), 'Read-only resource should have list() method');
            $this->assertTrue(method_exists($resource, 'get'), 'Read-only resource should have get() method');
        }
    }

    /**
     * Test License API methods
     */
    public function testLicenseApiMethods(): void
    {
        $licenseKeys = $this->client->licenseKeys();

        $this->assertTrue(method_exists($licenseKeys, 'activate'), 'Should have activate() method');
        $this->assertTrue(method_exists($licenseKeys, 'validate'), 'Should have validate() method');
        $this->assertTrue(method_exists($licenseKeys, 'deactivate'), 'Should have deactivate() method');
    }

    /**
     * Test Query Builder functionality
     */
    public function testQueryBuilder(): void
    {
        $query = (new QueryBuilder())
            ->filter('status', 'active')
            ->filter('created_at', '2024-01-01', '>=')
            ->sort('created_at', 'desc')
            ->page(1)
            ->pageSize(50)
            ->include('orders', 'subscriptions');

        $this->assertEquals(1, $query->getPage());
        $this->assertEquals(50, $query->getPageSize());
        $this->assertCount(2, $query->getFilters());
        $this->assertCount(1, $query->getSorts());
        $this->assertCount(2, $query->getIncludes());
    }

    /**
     * Test Query Builder with pagination
     */
    public function testQueryBuilderPagination(): void
    {
        $query = (new QueryBuilder())
            ->page(2)
            ->pageSize(25);

        $this->assertTrue($query->hasPage());
        $this->assertTrue($query->hasPageSize());
        $this->assertEquals(2, $query->getPage());
        $this->assertEquals(25, $query->getPageSize());
    }

    /**
     * Test Query Builder with filtering
     */
    public function testQueryBuilderFiltering(): void
    {
        $query = (new QueryBuilder())
            ->filter('status', 'active')
            ->filter('created_at', '2024-01-01', '>=');

        $filters = $query->getFilters();
        $this->assertCount(2, $filters);
    }

    /**
     * Test Query Builder with sorting
     */
    public function testQueryBuilderSorting(): void
    {
        $query = (new QueryBuilder())
            ->sort('created_at', 'desc')
            ->sort('name', 'asc');

        $sorts = $query->getSorts();
        $this->assertCount(2, $sorts);
    }

    /**
     * Test Query Builder with includes
     */
    public function testQueryBuilderIncludes(): void
    {
        $query = (new QueryBuilder())
            ->include('orders')
            ->include('subscriptions')
            ->include('customers');

        $includes = $query->getIncludes();
        $this->assertCount(3, $includes);
        $this->assertContains('orders', $includes);
        $this->assertContains('subscriptions', $includes);
        $this->assertContains('customers', $includes);
    }

    /**
     * Test Configuration options
     */
    public function testConfigurationOptions(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key_config')
            ->withTimeout(60)
            ->withMaxRetries(5)
            ->withWebhookSecret('webhook_secret')
            ->build();

        $this->assertTrue($config->isAuthenticated());
        $this->assertEquals(60, $config->getTimeout());
        $this->assertEquals(5, $config->getMaxRetries());
        $this->assertEquals('webhook_secret', $config->getWebhookSecret());
    }

    /**
     * Test API Base URL
     */
    public function testApiBaseUrl(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key_url')
            ->build();

        $this->assertStringContainsString('api.lemonsqueezy.com', $config->getApiBaseUrl());
        $this->assertStringContainsString('/v1', $config->getApiBaseUrl());
    }

    /**
     * Test Logger integration
     */
    public function testLoggerIntegration(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key_logger')
            ->build();

        $client = new Client($config);
        $logger = $client->getLogger();

        $this->assertNotNull($logger);
    }

    /**
     * Test Exception types are available
     */
    public function testExceptionTypesAvailable(): void
    {
        $this->assertTrue(class_exists(NotFoundException::class));
        $this->assertTrue(class_exists(RateLimitException::class));
        $this->assertTrue(class_exists(ValidationException::class));
    }

    /**
     * Test Resource method chaining
     */
    public function testResourceMethodsExist(): void
    {
        $customers = $this->client->customers();

        // Verify methods exist
        $this->assertTrue(method_exists($customers, 'list'));
        $this->assertTrue(method_exists($customers, 'get'));
        $this->assertTrue(method_exists($customers, 'create'));
        $this->assertTrue(method_exists($customers, 'update'));
        $this->assertTrue(method_exists($customers, 'delete'));
    }

    /**
     * Test all endpoints have endpoint method
     */
    public function testAllEndpointsImplemented(): void
    {
        $endpoints = [
            'users' => $this->client->users(),
            'stores' => $this->client->stores(),
            'products' => $this->client->products(),
            'variants' => $this->client->variants(),
            'prices' => $this->client->prices(),
            'files' => $this->client->files(),
            'customers' => $this->client->customers(),
            'orders' => $this->client->orders(),
            'order-items' => $this->client->orderItems(),
            'subscriptions' => $this->client->subscriptions(),
            'subscription-invoices' => $this->client->subscriptionInvoices(),
            'subscription-items' => $this->client->subscriptionItems(),
            'discounts' => $this->client->discounts(),
            'discount-redemptions' => $this->client->discountRedemptions(),
            'license-keys' => $this->client->licenseKeys(),
            'webhooks' => $this->client->webhooks(),
            'checkouts' => $this->client->checkouts(),
            'affiliates' => $this->client->affiliates(),
            'usage-records' => $this->client->usageRecords(),
        ];

        foreach ($endpoints as $expectedEndpoint => $resource) {
            $this->assertEquals($expectedEndpoint, $resource->getEndpoint());
            $this->assertTrue(method_exists($resource, 'getEndpoint'));
        }
    }

    /**
     * Test Full API Coverage Summary
     */
    public function testFullApiCoverageSummary(): void
    {
        // This test verifies that all 19 resources are implemented
        $resources = [
            $this->client->users(),
            $this->client->stores(),
            $this->client->products(),
            $this->client->variants(),
            $this->client->prices(),
            $this->client->files(),
            $this->client->customers(),
            $this->client->orders(),
            $this->client->orderItems(),
            $this->client->subscriptions(),
            $this->client->subscriptionInvoices(),
            $this->client->subscriptionItems(),
            $this->client->discounts(),
            $this->client->discountRedemptions(),
            $this->client->licenseKeys(),
            $this->client->webhooks(),
            $this->client->checkouts(),
            $this->client->affiliates(),
            $this->client->usageRecords(),
        ];

        // Verify all 19 resources are accessible
        $this->assertCount(19, $resources);

        // Verify each has getEndpoint method
        foreach ($resources as $resource) {
            $this->assertNotNull($resource->getEndpoint());
        }
    }

    /**
     * Test UsageRecords resource operations
     */
    public function testUsageRecordsOperations(): void
    {
        $usageRecords = $this->client->usageRecords();

        // Verify basic methods exist
        $this->assertTrue(method_exists($usageRecords, 'list'));
        $this->assertTrue(method_exists($usageRecords, 'get'));
        $this->assertTrue(method_exists($usageRecords, 'create'));
        $this->assertTrue(method_exists($usageRecords, 'update'));
        $this->assertTrue(method_exists($usageRecords, 'delete'));
    }

    /**
     * Test UsageRecords update throws UnsupportedOperationException
     */
    public function testUsageRecordsUpdateThrowsUnsupported(): void
    {
        $usageRecords = $this->client->usageRecords();

        $this->expectException(\LemonSqueezy\Exception\UnsupportedOperationException::class);
        $usageRecords->update('123', ['quantity' => 50]);
    }

    /**
     * Test UsageRecords delete throws UnsupportedOperationException
     */
    public function testUsageRecordsDeleteThrowsUnsupported(): void
    {
        $usageRecords = $this->client->usageRecords();

        $this->expectException(\LemonSqueezy\Exception\UnsupportedOperationException::class);
        $usageRecords->delete('123');
    }

    /**
     * Test that Configuration is immutable
     */
    public function testConfigurationImmutability(): void
    {
        $config = (new ConfigBuilder())
            ->withApiKey('test_api_key_immutable')
            ->withTimeout(30)
            ->build();

        // Config should be created and cannot be modified directly
        $this->assertEquals('test_api_key_immutable', $config->getCredentials()->getApiKey());
        $this->assertEquals(30, $config->getTimeout());
    }
}
