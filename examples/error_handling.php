<?php

/**
 * Error Handling Example
 *
 * Demonstrates how to handle various exception types
 * thrown by the API client.
 */

require __DIR__ . '/../vendor/autoload.php';

use LemonSqueezy\ClientFactory;
use LemonSqueezy\Exception\{
    RateLimitException,
    NotFoundException,
    UnauthorizedException,
    ValidationException,
    AuthenticationException,
    ClientException,
    ServerException,
    HttpException,
    LemonSqueezyException
};

$client = ClientFactory::create('YOUR_API_KEY');

// Example 1: Handle 404 Not Found
echo "=== Handling Not Found ===\n";
try {
    $customer = $client->customers()->get('nonexistent-id');
} catch (NotFoundException $e) {
    echo "Customer not found: " . $e->getMessage() . "\n";
    echo "Response data: " . json_encode($e->getResponse()) . "\n";
}

// Example 2: Handle rate limiting
echo "\n=== Handling Rate Limit ===\n";
try {
    // Make many requests...
    for ($i = 0; $i < 400; $i++) {
        $customers = $client->customers()->list();
    }
} catch (RateLimitException $e) {
    echo "Rate limit exceeded: " . $e->getMessage() . "\n";
    echo "Remaining requests: " . $e->getRemainingRequests() . "\n";
    echo "Reset time: " . $e->getResetTime()->format('Y-m-d H:i:s') . "\n";
    echo "Seconds to wait: " . $e->getSecondsUntilReset() . "\n";

    // Wait and retry
    sleep($e->getSecondsUntilReset());
    $customers = $client->customers()->list();
}

// Example 3: Handle authentication errors
echo "\n=== Handling Authentication Error ===\n";
try {
    // This will fail with invalid API key
    $badClient = ClientFactory::create('invalid_api_key_12345');
    $users = $badClient->users()->list();
} catch (UnauthorizedException $e) {
    echo "Unauthorized: " . $e->getMessage() . "\n";
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage() . "\n";
}

// Example 4: Handle validation errors
echo "\n=== Handling Validation Error ===\n";
try {
    $discount = $client->discounts()->create([
        'invalid_field' => 'value',
    ]);
} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage() . "\n";
    if ($e->getErrors()) {
        echo "Errors: " . json_encode($e->getErrors(), JSON_PRETTY_PRINT) . "\n";
    }
}

// Example 5: Handle server errors
echo "\n=== Handling Server Error ===\n";
try {
    $customers = $client->customers()->list();
} catch (ServerException $e) {
    echo "Server error: " . $e->getMessage() . "\n";
    echo "Status code: " . $e->getCode() . "\n";
}

// Example 6: Generic error handling
echo "\n=== Generic Error Handling ===\n";
try {
    $customers = $client->customers()->list();
} catch (ClientException $e) {
    echo "Client error (4xx): " . $e->getMessage() . "\n";
} catch (ServerException $e) {
    echo "Server error (5xx): " . $e->getMessage() . "\n";
} catch (HttpException $e) {
    echo "HTTP error: " . $e->getMessage() . "\n";
} catch (LemonSqueezyException $e) {
    echo "LemonSqueezy error: " . $e->getMessage() . "\n";
    if ($response = $e->getResponse()) {
        echo "Response: " . json_encode($response) . "\n";
    }
} catch (\Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}

echo "\nError handling examples completed!\n";
