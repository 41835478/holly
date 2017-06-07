<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'gravatar' => [
        'host' => 'http://cdn.v2ex.com/gravatar',
        // 'host' => 'http://gravatar.css.network/avatar',
        'default' => 'identicon',
        'rating' => 'pg',
    ],

    'google_analytics' => null,

    'mobsms' => [
        'key' => env('MOBSMS_KEY'),
    ],

    'xgpush' => [
        'key' => env('XGPUSH_KEY'),
        'secret' => env('XGPUSH_SECRET'),
        'environment' => env('XGPUSH_ENVIRONMENT', env('APP_ENV')),
        'custom' => 'my',
        'account_prefix' => 'user',
    ],

];
