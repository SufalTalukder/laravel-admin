<?php

return [
    'paths' => [
        'api/*',
        'v1/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://192.168.68.107:5173',
        'http://192.168.1.4:5173',
        'http://192.168.1.7:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-API-KEY',
        'X-API-TOKEN',
        'accessToken',
        'refreshToken',
        'Accept',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
