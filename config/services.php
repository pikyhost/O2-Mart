<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    // config/services.php

    'paymob' => [
        'api_key'        => env('PAYMOB_API_KEY'),
        'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        'iframe_id'      => env('PAYMOB_IFRAME_ID'),
        'base_url'       => env('PAYMOB_BASE_URL', 'https://uae.paymob.com'),
        'redirection_url' => env('PAYMOB_REDIRECTION_URL'),
        'notification_url' => env('PAYMOB_NOTIFICATION_URL'),
        'frontend_redirect_url' => env('PAYMOB_FRONTEND_REDIRECT_URL', 'http://localhost:3000'),
        'env'            => env('PAYMOB_ENV', 'test'),
    ],

    'jeebly' => [
        'base_url'   => env('JEEBLY_BASE_URL', 'https://demo.jeebly.com'),
        'username'   => env('JEEBLY_USERNAME'),
        'password'   => env('JEEBLY_PASSWORD'),
        'api_key'    => env('JEEBLY_API_KEY'),
        'client_key' => env('JEEBLY_CLIENT_KEY'),
    ],



];
