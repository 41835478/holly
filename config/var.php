<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Migration And Seeds Variables
    |--------------------------------------------------------------------------
    */

    'super_admin_email' => env('SUPER_ADMIN_EMAIL'),

    'initial_user_id' => env('INITIAL_USER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Validation Variables
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'verify_phone_excepts' => [
            // The phone numbers that should be excluded from verification.
            'phones' => array_filter(explode(',', env('VERIFY_PHONE_EXCEPTS_PHONES'))),
            // The verification codes that should be excluded from verification.
            'codes' => array_filter(explode(',', env('VERIFY_PHONE_EXCEPTS_CODES'))),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | iOS App Variables
    |--------------------------------------------------------------------------
    */

    'ios' => [
        'app_store_id' => '123456',

        'app_store_url' => 'https://itunes.apple.com/cn/app/id123456?l=zh&ls=1&mt=8',

        // 应用宝的下载地址。主要用于微信浏览器的下载跳转。
        'app_qq_url' => 'http://a.app.qq.com/o/ioslink.jsp?id=123456',

        // The Shared Secret for the In App Purchase
        'iap_shared_secret' => '6acb115306e94c89a2f21b48ca072cf8',

        // app版本。
        // current: 当前已上线的最新版本。
        // way:     版本更新方式。 1: 可选更新， 2: 强制更新。
        'app_version' => [
            'current' => [
                'version' => '1.0.0',
                'way' => 1,
                'desc' => "新版本更新说明",
                'url' => 'https://itunes.apple.com/cn/app/id123456?l=zh&ls=1&mt=8',
            ],
            'dev' => [
                'version' => '1.0.0-dev',
                'way' => 1,
                'desc' => "Dev 开发版本更新",
                'url' => env('APP_URL'),
            ],
            'beta' => [
                'version' => '1.0.0-beta3',
                'way' => 2,
                'desc' => 'Beta 测试版本更新',
                'url' => 'itms-services://?action=download-manifest&url=https%3A%2F%2Fips.pre.im%2Fapp%2Fplist%2F'.'b41d3e2a0dcd635f56eb236e2c801ef8',
            ],
        ],

        // The iOS app version which is being reviewed by the App Store Reviewing Team.
        // After reviewing approved, you can change it to a non-existent version.
        'app_store_reviewing_version' => env('IOS_APP_STORE_REVIEWING_VERSION', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Android App Variables
    |--------------------------------------------------------------------------
    */

    'android' => [
        'app_version' => [
            'current' => [
                'version' => '1.0.0',
                'way' => 1,
                'desc' => "新版本更新说明",
                'url' => 'http://',
            ],
        ],
    ],

];
