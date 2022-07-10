<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Version of the application
    |--------------------------------------------------------------------------
    |
    | This value returns the current version of the application.
    |
    */

    'app_version' => is_file(__DIR__.'/.version') ? file_get_contents(__DIR__.'/.version') : (is_dir(__DIR__.'/../.git') ? trim(exec('git --git-dir '.base_path('.git').' describe --abbrev=0 --tags')) : ''),

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

    /*
    |--------------------------------------------------------------------------
    | Monica and OfficeLife billing links
    |--------------------------------------------------------------------------
    */

    'billing_links' => [
        'monica' => 'https://app.monicahq.com/settings/billing',
        'officelife' => 'https://app.officelife.io/settings/billing',
    ],
];
