<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for product management and display.
    |
    */

    'pagination' => [
        'default_per_page' => 12,
        'max_per_page' => 100,
        'available_per_page' => [12, 24, 48, 96],
    ],

    'search' => [
        'min_query_length' => 2,
        'max_query_length' => 255,
        'default_limit' => 10,
        'max_limit' => 50,
    ],

    'filters' => [
        'price' => [
            'min' => 0,
            'max' => 100000,
            'step' => 100,
        ],
        'sort_options' => [
            'newest' => 'Новинки',
            'price_asc' => 'Цена: по возрастанию',
            'price_desc' => 'Цена: по убыванию',
            'name_asc' => 'Название: А-Я',
            'name_desc' => 'Название: Я-А',
        ],
    ],

    'images' => [
        'thumbnail' => [
            'width' => 300,
            'height' => 200,
            'quality' => 85,
        ],
        'lazy_loading' => true,
        'webp_support' => true,
    ],

    'cache' => [
        'ttl' => 3600, // 1 hour
        'tags' => ['products', 'categories', 'manufacturers'],
    ],

    'api' => [
        'rate_limit' => 60, // requests per minute
        'version' => 'v1',
    ],
];
