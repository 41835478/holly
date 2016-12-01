<?php

return [

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
    | Characters for `App\Support\Helper::int2string()` and `App\Support\Helper::string2int()`.
    | You may generate it via `php artisan key:int2string-characters`.
    |
    */

    'int2string_characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

];
