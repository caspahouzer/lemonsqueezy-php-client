# Changelog

All notable changes to the LemonSqueezy PHP API Client are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
-   Request retry with exponential backoff
-   Async/await support with ReactPHP
-   Laravel service provider
-   Symfony bundle
-   PHPStan configuration for strict type checking

---

For detailed information about each release, see the [Releases](https://github.com/caspahouzer/lemonsqueezy-php-client/releases) page.
