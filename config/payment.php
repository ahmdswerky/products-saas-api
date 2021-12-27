<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways keys
    |--------------------------------------------------------------------------
    */

    'gateways' => [
        'stripe' => [
            'publishable_key' => env('STRIPE_KEY'),
            'client_id' => env('STRIPE_CLIENT_ID'),
            'secret' => env('STRIPE_SECRET'),
        ],
        'paypal' => [
            'api_url' => env('PAYPAL_API_URL'),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'partner_id' => env('PAYPAL_PARTNER_MERCHANT_ID'),
            'bn_code' => env('PAYPAL_BN_CODE'),
        ],
    ],

    'currency' => 'usd',
];
