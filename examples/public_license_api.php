<?php
/**
 * Public License API Example
 *
 * Demonstrates how to use the public License API endpoints
 * (no authentication required).
 */

require __DIR__ . '/../vendor/autoload.php';

use LemonSqueezy\Configuration\ConfigBuilder;
use LemonSqueezy\Client;
use LemonSqueezy\Exception\LemonSqueezyException;

// Create client for public API (no API key needed)
$config = (new ConfigBuilder())->build();
$client = new Client($config);

try {
    // Activate a license key
    // Creates a new instance activation for a license key
    echo "=== Activating License ===\n";
    $activation = $client->licenseKeys()->activate(
        'your-license-key-here',
        'example.com' // instance name (usually domain or device name)
    );
    echo "Activation successful!\n";
    if (is_array($activation)) {
        echo "Instance ID: " . ($activation['instance_id'] ?? 'N/A') . "\n";
        echo "Times activated: " . ($activation['times_activated'] ?? 'N/A') . "\n";
        echo "Max activations: " . ($activation['times_activated_max'] ?? 'N/A') . "\n";
    }

    // Validate a license key
    // Checks if a license key is valid (optionally for a specific instance)
    echo "\n=== Validating License ===\n";
    $validation = $client->licenseKeys()->validate(
        'your-license-key-here'
        // Optionally pass instance ID to validate specific instance: , 'instance-id-from-activation'
    );
    echo "Validation completed!\n";
    if (is_array($validation)) {
        echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
        echo "Times activated: " . ($validation['times_activated'] ?? 'N/A') . "\n";
    }

    // Deactivate a license instance
    // Removes an activation (instance) from a license key
    echo "\n=== Deactivating License Instance ===\n";
    $deactivation = $client->licenseKeys()->deactivate(
        'your-license-key-here',
        'instance-id-from-activation' // The instance ID returned from activation
    );
    echo "Deactivation successful!\n";
    if (is_array($deactivation)) {
        echo "Times activated: " . ($deactivation['times_activated'] ?? 'N/A') . "\n";
    }

} catch (LemonSqueezyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
