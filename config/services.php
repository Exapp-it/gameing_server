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

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT')
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'expay' => [
        'uri'     => env('EXPAY_API_URI'),
        'public'  => env('EXPAY_API_PUBLIC'),
        'private' => env('EXPAY_API_PRIVATE')
    ],

    'tom_horn' => [
        'integration_service' => env('TOM_HORN_API_INTEGRATION_SERVICE'),
        'report_service'      => env('TOM_HORN_API_REPORT_SERVICE'),
        'partner_id'          => env('TOM_HORN_API_PARTNER_ID'),
        'secret_key'          => env('TOM_HORN_API_SECRET_KEY'),
    ],

    'mancala' => [
        'uri'          => env('MANCALA_URI'),
        'guid'         => env('MANCALA_GUID'),
        'key'          => env('MANCALA_KEY'),
    ],

    'b2bslots' => [
        'url' => env('B2B_SLOTS_URL'),
        'operator_id' => env('B2B_SLOTS_OPERATOE_ID'),
    ],
];
