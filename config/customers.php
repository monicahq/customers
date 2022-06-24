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
    | This is 32 bits random string. You can generate your own by running:
    | `echo -n 'base64:'; openssl rand -base64 32`
    |
    */

    'key' => env('PRIVATE_KEY'),

    'cipher' => 'AES-256-GCM',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Country
    |--------------------------------------------------------------------------
    |
    | This is the default country if the user did not defined one.
    |
    */

    'fallback_country' => 'US',

];
