<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Private key to encrypt data
    |--------------------------------------------------------------------------
    |
    | This value is used to encrypt the instance key. If it's not set, the key
    | can be hacked by anyone.
    |
    */

    'key' => env('PRIVATE_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Secret key to communicate with the instances
    |--------------------------------------------------------------------------
    |
    | We need to communicate with the customer portal to check licence keys.
    | This is done through an HTTP call that we need to secure.
    |
    */

    'customer_portal_secret_key' => env('CUSTOMER_PORTAL_SECRET_KEY', null),
];
