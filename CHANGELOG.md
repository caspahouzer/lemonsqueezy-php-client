# Changelog

All notable changes to the LemonSqueezy PHP API Client are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2026-01-05

### Added

-   **Special API Operations** - Support for non-CRUD API endpoints:
    -   `Orders::generateInvoice()` - Generate invoices for orders (POST /orders/{id}/invoices)
    -   `Orders::issueRefund()` - Issue refunds for orders (POST /orders/{id}/refunds)
    -   `Subscriptions::cancelSubscription()` - Cancel subscriptions (POST /subscriptions/{id}/cancel)
    -   `SubscriptionItems::getCurrentUsage()` - Get current usage data for subscription items (GET /subscription-items/{id}/current-usage)
    -   `SubscriptionInvoices::generateInvoice()` - Generate subscription invoices (POST /subscription-invoices/{id}/generate)

-   **API Coverage Improvements**:
    -   Comprehensive API capability audit against official LemonSqueezy API documentation
    -   Accurate CRUD operation support matrix for all 18 resources
    -   Proper error handling for unsupported operations via `UnsupportedOperationException`

-   **Documentation**:
    -   Added special API operations section to API_COVERAGE.md with usage examples
    -   Updated resource documentation to clearly indicate read-only vs writable resources
    -   Added usage examples for all special operations (invoices, refunds, cancellation, usage tracking)

### Fixed

-   **API Capability Compliance** - Fixed misleading CRUD operation support:
    -   Products, Variants, Prices, Files, Orders, Order Items, Subscription Invoices, Users, Stores, Affiliates, and Discount Redemptions now properly indicate they are read-only
    -   Subscriptions now properly indicates that create and delete are not supported
    -   Subscription Items now properly indicates that create and delete are not supported
    -   Checkouts now properly indicates that update and delete are not supported
    -   Customers now properly indicates that delete is not supported

### Technical Details

-   All special operations follow consistent endpoint construction pattern with proper URL encoding
-   All special operations include @see links to official LemonSqueezy API documentation
-   Batch operation tests updated to use only supported operations (Customers and Discounts)
-   Full backward compatibility maintained

## [1.1.0] - 2026-01-05

### Added

-   **Batch Operations Support** - New feature for efficient bulk processing of resources:
    -   `batch()` method to execute mixed batch operations
    -   `batchCreate()` convenience method for creating multiple resources
    -   `batchUpdate()` convenience method for updating multiple resources
    -   `batchDelete()` convenience method for deleting multiple resources
    -   Sequential execution with intelligent rate limiting (respects 300 req/min limit)
    -   Partial failure handling with detailed error tracking per operation
    -   Configurable delays (delayMs), timeouts, and stop-on-error behavior
    -   Comprehensive BatchResult container with success/failure tracking and statistics
    -   Support for mixed operation types in a single batch

-   **Batch Operation Classes**:
    -   `BatchCreateOperation` - Define create operations
    -   `BatchUpdateOperation` - Define update operations
    -   `BatchDeleteOperation` - Define delete operations
    -   `BatchResult` - Result container with success/failure tracking and summaries
    -   `BatchConfig` - Configuration and validation for batch execution

-   **Exception Handling**:
    -   `BatchException` - Batch-specific exception with partial result tracking

-   **Comprehensive Testing**:
    -   `BatchResultTest` - 14 tests for result container functionality
    -   `BatchOperationTest` - 10 tests for operation classes
    -   `BatchConfigTest` - 12 tests for configuration validation
    -   `BatchIntegrationTest` - 17 tests for client integration

-   **Documentation**:
    -   Added batch operations section to API_COVERAGE.md
    -   Included usage examples for all batch operation methods
    -   Documented rate limiting and error handling strategies

### Technical Details

-   Sequential batch execution ensures rate limit compliance
-   Default 200ms delay between operations (5 ops/sec, safe for 300 req/min limit)
-   Fluent builder pattern for batch operations
-   Full backward compatibility - no breaking changes
-   All batch files pass PHP syntax validation

## [1.0.5] - 2026-01-05

### Fixed

-   Corrected a syntax error in the `release.yml` GitHub Actions workflow.

## [1.0.4] - 2026-01-05

### Fixed

-   Corrected a syntax error in the `release.yml` GitHub Actions workflow.

## [1.0.3] - 2026-01-04

### Fixed

-   Corrected a syntax error in the `release.yml` GitHub Actions workflow.

## [1.0.2] - 2026-01-04

### Added

-   Response caching middleware to improve performance for repeated GET requests.
-   Automated release process using GitHub Actions.

### Fixed

-   Corrected PSR-16 incompatibility in `FileCache` implementation.
-   Added missing `psr/simple-cache` dependency to `composer.json`.

## [1.0.1] - 2026-01-04

### Added

-   Request logging middleware for debugging API calls

## [1.0.0] - 2026-01-04

### Added

-   **Initial Release** - Full PSR-4 compliant PHP API client for LemonSqueezy REST API
-   **19 API Resources** - Complete coverage of all LemonSqueezy endpoints:

    -   Users (with `me()` method for current user)
    -   Stores
    -   Products
    -   Variants
    -   Prices
    -   Files
    -   Customers
    -   Orders
    -   Order Items
    -   Subscriptions
    -   Subscription Invoices
    -   Subscription Items
    -   Discounts
    -   Discount Redemptions
    -   License Keys (public API support)
    -   Webhooks
    -   Checkouts
    -   Affiliates

-   **Authentication** - Multiple authentication strategies:

    -   Bearer Token authentication for API requests
    -   Public License API support (no authentication required)

-   **Query Building** - Fluent query builder with support for:

    -   Pagination (page and page size)
    -   Filtering (multiple filters with operators)
    -   Sorting (ascending/descending with automatic snake_case to camelCase conversion)
    -   Relationship inclusion

-   **Error Handling** - Comprehensive exception hierarchy:

    -   `NotFoundException` for 404 responses
    -   `UnauthorizedException` for 401 responses
    -   `RateLimitException` with reset time tracking
    -   `ValidationException` for input errors
    -   `ClientException` for 4xx errors
    -   `ServerException` for 5xx errors
    -   `HttpException` for network errors

-   **Middleware System** - Extensible middleware pipeline:

    -   Authentication middleware
    -   Rate limiting middleware
    -   Request/response logging

-   **Model Hydration** - Automatic JSON:API deserialization:

    -   19 entity models for all resources
    -   Attribute access with dot notation
    -   Relationship lazy loading

-   **Pagination Support**:

    -   Page-based pagination
    -   Automatic pagination metadata extraction
    -   Iterator support for lazy loading pages
    -   `hasNextPage()` and `hasPreviousPage()` methods

-   **Dependency Injection** - PSR-compliant interfaces:

    -   PSR-18 HTTP Client interface
    -   PSR-17 HTTP Factory interface
    -   PSR-7 HTTP Message interface
    -   PSR-3 Logger interface

-   **Composer Package** - Published as `slk/lemonsqueezy-api-client`:

    -   Automatic Guzzle HTTP client fallback
    -   Zero dependencies (only PSR interfaces)
    -   Support for PHP 8.0+

-   **Comprehensive Documentation**:

    -   Installation guide
    -   Quick start guide
    -   API resource documentation
    -   Model documentation
    -   Pagination guide
    -   Error handling guide
    -   Multiple working examples

-   **Integration Testing**:

    -   Real API integration tests
    -   Test mode support
    -   Test card numbers for order testing
    -   Proper error handling verification

-   **Development Tools**:
    -   PHPUnit test suite
    -   PHPStan static analysis
    -   PHP-CS-Fixer code formatting
    -   GitHub Actions CI/CD workflows

### Technical Details

-   **API Compliance**: JSON:API specification compliant
-   **Rate Limiting**: Built-in 300 requests/minute tracking
-   **Pagination Format**: Automatic conversion from API format to client format
-   **Field Naming**: Automatic snake_case to camelCase conversion for API fields
-   **Response Parsing**: Guzzle exception handling for proper error conversion

## Future Releases

### [Planned Features]

-   Webhook signature verification utilities
-   Response caching middleware
-   Response caching middleware
-   Request retry with exponential backoff
-   Async/await support with ReactPHP
-   Laravel service provider
-   Symfony bundle
-   PHPStan configuration for strict type checking

---

For detailed information about each release, see the [Releases](https://github.com/caspahouzer/lemonsqueezy-php-client/releases) page.
