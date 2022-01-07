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

    'private_key_to_encrypt_instance_keys' => env('PRIVATE_KEY_TO_ENCRYPT_INSTANCE_KEYS', ''),
];
