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
    // Activate a license
    echo "=== Activating License ===\n";
    $activation = $client->licenseKeys()->activate(
        'your-license-key',
        'example.com' // instance name (usually domain)
    );
    echo "Activation successful!\n";
    echo "Instance ID: " . $activation['instance_id'] . "\n";
    echo "Times activated: " . $activation['times_activated'] . "\n";
    echo "Max activations: " . $activation['times_activated_max'] . "\n";

    // Validate a license
    echo "\n=== Validating License ===\n";
    $validation = $client->licenseKeys()->validate(
        'your-license-key',
        'instance-id-from-above',
        'example.com'
    );
    echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
    echo "Times activated: " . $validation['times_activated'] . "\n";

    // Deactivate a license
    echo "\n=== Deactivating License ===\n";
    $deactivation = $client->licenseKeys()->deactivate(
        'your-license-key',
        'instance-id-from-above',
        'example.com'
    );
    echo "Deactivation successful!\n";
    echo "Times activated: " . $deactivation['times_activated'] . "\n";

} catch (LemonSqueezyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
