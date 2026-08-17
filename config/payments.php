<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | The active payment provider. In development use "sandbox". In
    | production set this to the Tanzanian gateway adapter name.
    |
    */

    'provider' => env('PAYMENT_PROVIDER', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Provider Credentials
    |--------------------------------------------------------------------------
    |
    | Secret keys must only ever live in your .env file. Never expose these
    | values in Blade templates or frontend JavaScript.
    |
    */

    'api_key' => env('PAYMENT_API_KEY'),
    'secret' => env('PAYMENT_SECRET'),

    'callback_url' => env('PAYMENT_CALLBACK_URL', '/payment/callback'),
    'webhook_url' => env('PAYMENT_WEBHOOK_URL', '/payment/webhook'),

    'sandbox' => [
        'enabled' => env('PAYMENT_SANDBOX', true),
    ],
];
