<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        env('BACKEND_URL', 'https://o2mart.to7fa.online'),
        'https://mart.to7fa.online',
        'https://o22mart.to7fa.online',
        'https://o2-mart-front.vercel.app',
        'http://78.110.164.71:3000',
        env('FRONTEND_URL', 'https://mk3bel.o2mart.net')
    ],

// comment
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'X-API-Key', 'X-Session-ID'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
