<?php
/**
 * Query Builder Example
 *
 * Demonstrates filtering, sorting, pagination, and relationships.
 */

require __DIR__ . '/../vendor/autoload.php';

use LemonSqueezy\ClientFactory;
use LemonSqueezy\Query\QueryBuilder;
use LemonSqueezy\Exception\LemonSqueezyException;

$client = ClientFactory::create('YOUR_API_KEY');

try {
    // Simple pagination
    echo "=== Basic Pagination ===\n";
    $query = (new QueryBuilder())
        ->page(1)
        ->pageSize(10);

    $customers = $client->customers()->list($query);
    echo "Page: " . $customers->getCurrentPage() . "\n";
    echo "Per page: " . $customers->getPerPage() . "\n";
    echo "Total: " . $customers->getTotal() . "\n";
    echo "Has next: " . ($customers->hasNextPage() ? 'Yes' : 'No') . "\n";

    // Filtering and sorting
    echo "\n=== Filtering and Sorting ===\n";
    $query = (new QueryBuilder())
        ->filter('status', 'active')
        ->sort('created_at', 'desc')
        ->page(1)
        ->pageSize(20)
        ->include('orders', 'subscriptions');

    $customers = $client->customers()->list($query);
    echo "Found " . $customers->count() . " customers\n";

    foreach ($customers->items() as $customer) {
        echo "- " . $customer->getEmail() . "\n";
    }

    // Advanced filtering
    echo "\n=== Advanced Filtering ===\n";
    $query = (new QueryBuilder())
        ->filter('country', 'US')
        ->filter('created_at', '2024-01-01', '>=')
        ->sort('email', 'asc');

    $filtered = $client->customers()->list($query);
    echo "Found " . $filtered->count() . " US customers since 2024-01-01\n";

} catch (LemonSqueezyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
