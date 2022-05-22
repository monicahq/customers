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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
     * Socialite providers
     */

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI', '/auth/github/callback'),
    ],

    'monica' => [
        'host' =>  env('MONICA_HOST', 'https://app.monicahq.com'),
        'client_id' => env('MONICA_CLIENT_ID'),
        'client_secret' => env('MONICA_CLIENT_SECRET'),
        'redirect' => env('MONICA_REDIRECT_URI', '/auth/monica/callback'),
        'userinfo_key' => 'data',
        'userinfo_uri' => 'api/me',
    ],

    'officelife' => [
        'host' =>  env('OFFICELIFE_HOST', 'https://app.officelife.io'),
        'client_id' => env('OFFICELIFE_CLIENT_ID'),
        'client_secret' => env('OFFICELIFE_CLIENT_SECRET'),
        'redirect' => env('OFFICELIFE_REDIRECT_URI', '/auth/officelife/callback'),
        'userinfo_key' => 'data',
        'userinfo_uri' => 'api/me',
    ],

];
