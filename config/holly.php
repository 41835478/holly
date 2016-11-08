<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application URLs
    |--------------------------------------------------------------------------
    */

    'url' => [
        'site' => env('APP_URL'),
        'admin' => env('APP_URL_ADMIN', env('APP_URL')),
        'api' => env('APP_URL_API', env('APP_URL')),
        'asset' => env('APP_URL_ASSET', env('APP_URL')),
        'cdn' => env('APP_URL_CDN', env('APP_URL')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domains
    |--------------------------------------------------------------------------
    */

    'cookie_domain' => [
        'admin' => env('SESSION_DOMAIN_ADMIN', null),
        'api' => env('SESSION_DOMAIN_API', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentications Defaults
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'admin' => [
            'guard' => 'admin',
            'passwords' => 'admin_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */

    'api' => [
        'key' => [
            'code' => 'code',
            'message' => 'msg',
        ],
        'code' => [
            'success' => 1,
        ],
        'token' => [
            'key' => env('API_TOKEN_KEY'),
            'valid_interval' => 200,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Characters for int2string
    |--------------------------------------------------------------------------
    |
    | Characters for `Holly\Support\Helper::int2string()` and `Holly\Support\Helper::string2int()`.
    | You may generate it via `php artisan holly:int2string-characters`.
    |
    */

    'int2string_characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

    /*
    |--------------------------------------------------------------------------
    | Gravatar
    |--------------------------------------------------------------------------
    |
    | Configurations for `Holly\Support\Helper::gravatar()`.
    |
    */

    'gravatar' => [
        'host' => 'http://cdn.v2ex.com/gravatar',
        // 'host' => 'http://gravatar.css.network/avatar',
        'default' => 'identicon',
        'rating' => 'pg',
    ],

    /*
    |--------------------------------------------------------------------------
    | App Store reviewing version
    |--------------------------------------------------------------------------
    |
    | The iOS app version which is being reviewed by the App Store Reviewing Team.
    | After reviewing approved, you can change it to a non-existent version.
    |
    */

    'app_store_reviewing_version' => env('IOS_REVIEWING_APP_VERSION', null),

];
