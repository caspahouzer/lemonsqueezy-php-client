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
        'include_parent_classes' => true,
        'sort_class_properties' => true,
        'sort_class_constants' => true,
        'sort_class_methods' => false,
        'sort_class_traits' => false,
        'base_url' => 'https://caspahouzer.github.io/lemonsqueezy-api-client/',
        'source_url' => 'https://github.com/caspahouzer/lemonsqueezy-php-client/blob/main',
        'source_dir' => '/src',
        'parse_private' => false,
        'parse_protected' => true,
        'default_opened' => 'classes',
        'download' => true,
    ]
);
