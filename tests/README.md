# LemonSqueezy API Client - Test Suite

This directory contains comprehensive tests for the LemonSqueezy PHP API Client.

## Test Types

### 1. Unit Tests (`Unit/`)
Tests individual components without external dependencies.

**Run unit tests:**
```bash
composer test
```

**Or specifically:**
```bash
vendor/bin/phpunit tests/Unit/
```

**Tests Included:**
- `ClientTest.php` - Client instantiation and configuration
- `ApiEndpointsTest.php` - All 18 API resources and methods

### 2. Integration Tests - Real API (`Integration/RealApiTest.php`)
Tests actual API endpoints by connecting to the real LemonSqueezy API.

## Setting Up Real API Tests

### Prerequisites
1. A LemonSqueezy account (https://www.lemonsqueezy.com)
2. An API key from your LemonSqueezy dashboard

### Configuration

1. **Get your API key:**
   - Go to https://app.lemonsqueezy.com/settings/api
   - Create or copy an existing API key (starts with `lsq_live_` or `lsq_test_`)

2. **Set the environment variable:**

   **Option A: Export in terminal (temporary)**
   ```bash
   export LEMONSQUEEZY_API_KEY="lsq_live_YOUR_KEY_HERE"
   ```

   **Option B: Create .env file (permanent)**
   ```bash
   # Create .env in the LemonSqueezy root directory
   echo "LEMONSQUEEZY_API_KEY=lsq_live_YOUR_KEY_HERE" > .env
   ```

   **Option C: phpunit.xml**
   Edit `phpunit.xml` and add:
   ```xml
   <php>
       <env name="LEMONSQUEEZY_API_KEY" value="lsq_live_YOUR_KEY_HERE" />
   </php>
   ```

## Running Tests

### Run All Tests
```bash
composer test
```

### Run Unit Tests Only
```bash
vendor/bin/phpunit tests/Unit/
```

### Run Real API Tests Only
```bash
vendor/bin/phpunit tests/Integration/RealApiTest.php
```

### Run Specific Test
```bash
vendor/bin/phpunit tests/Integration/RealApiTest.php --filter testUsersEndpoint
```

### Run with Verbose Output
```bash
vendor/bin/phpunit --verbose
```

### Run with Coverage Report
```bash
composer test:coverage
```

## Real API Test Coverage

The `RealApiTest.php` tests the following endpoints:

✅ **Users**
- List users
- Get user

✅ **Stores** (CRUD)
- List stores
- Get store
- Create store
- Update store
- Delete store

✅ **Products** (CRUD)
- List products
- Get product

✅ **Customers** (CRUD)
- List customers
- Get customer

✅ **Orders** (Read-only)
- List orders

✅ **Subscriptions** (Read-only)
- List subscriptions

✅ **Discounts** (CRUD)
- List discounts
- Get discount

✅ **Webhooks** (CRUD)
- List webhooks

✅ **All 18 Resources**
- Verify all endpoints are accessible

✅ **Query Features**
- Pagination
- Filtering
- Sorting

✅ **Error Handling**
- 404 Not Found errors

✅ **Model Attributes**
- Attribute access
- Model hydration

## What Gets Tested

### Unit Tests (`ApiEndpointsTest.php`)
1. ✅ All 18 resources are accessible
2. ✅ All resources have correct endpoints
3. ✅ CRUD methods are available
4. ✅ Read-only resources have list/get
5. ✅ License API methods exist
6. ✅ Query builder functionality
7. ✅ Configuration options
8. ✅ API base URL
9. ✅ Logger integration
10. ✅ Exception types available
11. ✅ Full API coverage (18 resources)

### Real API Tests (`RealApiTest.php`)
1. ✅ API connectivity
2. ✅ Users endpoint
3. ✅ Stores endpoint (CRUD)
4. ✅ Products endpoint
5. ✅ Customers endpoint
6. ✅ Orders endpoint
7. ✅ Subscriptions endpoint
8. ✅ Discounts endpoint (CRUD)
9. ✅ Webhooks endpoint (CRUD)
10. ✅ All 18 endpoints exist
11. ✅ Pagination functionality
12. ✅ Filtering functionality
13. ✅ Sorting functionality
14. ✅ Error handling (404)
15. ✅ Model attributes

## Expected Output

### Successful Unit Test Run
```
PHPUnit 9.5.x by Sebastian Bergmann

Time: 00:01.234, Memory: 6.00 MB

OK (11 tests, 30 assertions)
```

### Successful Real API Test Run
```
PHPUnit 9.5.x by Sebastian Bergmann

✓ Successfully connected to LemonSqueezy API
✓ Users endpoint: Found X users
✓ Stores List: Found X stores
✓ Products: Found X products
✓ Customers: Found X customers
✓ Orders: Found X orders
✓ Subscriptions: Found X subscriptions
✓ Discounts: Found X discounts
✓ Webhooks: Found X webhooks
✓ All 18 API Endpoints Accessible
✓ Error Handling (404): Correctly caught NotFoundException
✓ Model Attributes
✓ Pagination, Filtering, Sorting working

Time: 00:15.234, Memory: 8.00 MB

OK (14 tests, 50+ assertions)

✓ ALL TESTS PASSED - API Client is fully functional!
```

## Troubleshooting

### "LEMONSQUEEZY_API_KEY environment variable not set"
The test skips automatically if no API key is set. Set the environment variable:
```bash
export LEMONSQUEEZY_API_KEY="lsq_live_YOUR_KEY"
vendor/bin/phpunit tests/Integration/RealApiTest.php
```

### "Invalid API key: Unauthorized"
Your API key is invalid. Check:
1. The key starts with `lsq_live_` or `lsq_test_`
2. The key is from your LemonSqueezy dashboard
3. The key hasn't been revoked

### "API error: [error message]"
Check the error message:
- Rate limit exceeded? Wait before running tests again
- Resource not found? Your account may not have that resource
- Other errors? Check LemonSqueezy API docs

### Tests Run Too Slowly
The real API tests make actual HTTP requests. To speed up testing:
1. Use unit tests for development: `composer test`
2. Run real API tests less frequently
3. Consider using the test API key (`lsq_test_`) for faster responses

## Test Configuration

**phpunit.xml** settings:
- Bootstrap: `tests/bootstrap.php` - Loads autoloader
- Coverage: Lines/functions/classes tracked
- Output: Verbose by default
- Timeout: 30 seconds per test

## Adding More Tests

To add tests for new features:

1. **Create a new test class:**
   ```php
   namespace LemonSqueezy\Tests\Unit;

   class MyNewTest extends TestCase {
       public function testSomething() {
           // Your test
       }
   }
   ```

2. **Place in appropriate directory:**
   - Unit tests: `tests/Unit/`
   - Integration tests: `tests/Integration/`

3. **Run the new tests:**
   ```bash
   vendor/bin/phpunit tests/Unit/MyNewTest.php
   ```

## Continuous Integration

These tests can be integrated with CI/CD pipelines:

```yaml
# GitHub Actions example
- name: Run tests
  run: |
    composer install
    export LEMONSQUEEZY_API_KEY=${{ secrets.LEMONSQUEEZY_API_KEY }}
    composer test
```

## Further Reading

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [LemonSqueezy API Docs](https://docs.lemonsqueezy.com/api)
- [API_COVERAGE.md](../API_COVERAGE.md) - Endpoint checklist

---

**Last Updated:** January 4, 2024
**API Client Version:** 1.0.0
