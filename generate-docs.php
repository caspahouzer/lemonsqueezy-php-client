<?php

/**
 * phpDocumentor Documentation Generator Script
 *
 * This script generates HTML documentation using phpDocumentor
 * Note: Due to a URI resolution bug in phpDocumentor 3.9+, we use the default
 * output directory (.phpdoc/build) and copy the results to the output directory.
 */

$baseDir = __DIR__;
$vendorDir = $baseDir . '/vendor';
$outputDir = $baseDir . '/output';
$phpdocBuildDir = $baseDir . '/.phpdoc/build';

// Create output directory if it doesn't exist
@mkdir($outputDir, 0755, true);

// Run phpDocumentor (uses default output directory .phpdoc/build/)
$phpdocPath = escapeshellarg($vendorDir . '/bin/phpdoc');
$srcPath = escapeshellarg('src');

// Change to the project directory and run phpdoc without specifying target
$command = "cd " . escapeshellarg($baseDir) . " && {$phpdocPath} run -d {$srcPath} 2>&1";

$output = shell_exec($command);
$returnCode = 0;

// Check if there were any critical errors
// Only flag as error if there's an actual failure (not just "Error" in output)
if (strpos($output, 'The base URI must be an absolute URI') !== false ||
    strpos($output, 'Fatal error') !== false ||
    (strpos($output, 'Exception') !== false && strpos($output, 'All done') === false)) {
    $returnCode = 1;
}

// Print output
echo $output;

// If successful, copy the generated docs from .phpdoc/build to output/
if ($returnCode === 0 && is_dir($phpdocBuildDir)) {
    // Remove old output directory contents if it exists
    if (is_dir($outputDir)) {
        array_map('unlink', glob("$outputDir/*.*"));
        foreach (glob("$outputDir/*", GLOB_ONLYDIR) as $dir) {
            array_map('unlink', glob("$dir/**/*.*", GLOB_BRACE));
            @rmdir($dir);
        }
    }

    // Copy from .phpdoc/build to output
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($phpdocBuildDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        $target = str_replace($phpdocBuildDir, $outputDir, $file);
        if ($file->isDir()) {
            @mkdir($target, 0755, true);
        } else {
            @copy($file->getRealPath(), $target);
        }
    }

    echo "\n✅ Documentation generated successfully!\n";
    echo "📁 Output directory: " . $outputDir . "\n";
    echo "🌐 Open: file://" . $outputDir . "/index.html\n";
} else {
    echo "\n❌ Error generating documentation\n";
    $returnCode = 1;
}

exit($returnCode);
