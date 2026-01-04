<?php

// Register autoloader
require dirname(__DIR__) . '/vendor/autoload.php';

// Define test constants
define('TEST_BASE_DIR', __DIR__);
define('FIXTURES_DIR', TEST_BASE_DIR . '/Integration/Fixtures');

// Load test helpers
require TEST_BASE_DIR . '/Unit/MockHelpers.php';
