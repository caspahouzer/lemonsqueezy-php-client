<?php

use Doctum\Doctum;
use Symfony\Component\Finder\Finder;

return new Doctum(
    Finder::create()
        ->files()
        ->name('*.php')
        ->in(__DIR__ . '/src'),
    [
        'title' => 'LemonSqueezy PHP API Client',
        'version' => '1.2.0',
        'build_dir' => __DIR__ . '/build/docs',
        'cache_dir' => __DIR__ . '/build/.doctum_cache',
        'theme' => 'default',
    ]
);
