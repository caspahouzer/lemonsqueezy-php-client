<?php
/**
 * Basic Authentication Example
 *
 * Demonstrates how to create a client with Bearer token authentication
 * and perform basic CRUD operations.
 */

require __DIR__ . '/../vendor/autoload.php';

use LemonSqueezy\ClientFactory;
use LemonSqueezy\Exception\LemonSqueezyException;

// Create a client with your API key
$client = ClientFactory::create('lsq_live_YOUR_API_KEY_HERE');

try {
    // List all customers
    echo "=== Listing Customers ===\n";
    $customers = $client->customers()->list();
    echo "Total customers: " . $customers->getTotal() . "\n";

    foreach ($customers->items() as $customer) {
        echo "- " . $customer->getEmail() . "\n";
    }

    // Get a specific customer
    echo "\n=== Getting Specific Customer ===\n";
    if ($customers->count() > 0) {
        $first = $customers->items()[0];
        $customer = $client->customers()->get($first->getId());
        echo "Name: " . $customer->getAttribute('name') . "\n";
        echo "Email: " . $customer->getEmail() . "\n";
    }

    // List products
    echo "\n=== Listing Products ===\n";
    $products = $client->products()->list();
    echo "Total products: " . $products->getTotal() . "\n";

    foreach ($products->items() as $product) {
        echo "- " . $product->getAttribute('name') . "\n";
    }

    // List orders
    echo "\n=== Listing Orders ===\n";
    $orders = $client->orders()->list();
    echo "Total orders: " . $orders->getTotal() . "\n";

} catch (LemonSqueezyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($e->getResponse()) {
        echo "Response: " . json_encode($e->getResponse(), JSON_PRETTY_PRINT) . "\n";
    }
}
