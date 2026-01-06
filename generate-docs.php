<?php

/**
 * Doctum Documentation Generator Script
 *
 * This script generates HTML documentation using Doctum
 */

require_once __DIR__ . '/vendor/autoload.php';

use Doctum\Console\Application;

// Create and run the Doctum CLI application
$app = new Application();

// Execute the render command
$input = new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'render',
    'config' => __DIR__ . '/doctum.php',
    '--force' => true,
    '--no-interaction' => true,
]);

$output = new \Symfony\Component\Console\Output\ConsoleOutput();

try {
    $exitCode = $app->run($input, $output);
    if ($exitCode === 0) {
        echo "\n✅ Documentation generated successfully!\n";
        echo "📁 Output directory: " . __DIR__ . "/build/docs\n";
        echo "🌐 Open: file://" . __DIR__ . "/build/docs/index.html\n";
    }
    exit($exitCode);
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
