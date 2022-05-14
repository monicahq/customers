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

];
