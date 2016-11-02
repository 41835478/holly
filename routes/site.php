<?php

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');

Route::get('/', 'HomeController@index');

Route::group(['namespace' => 'Auth'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('login', 'LoginController@showLoginForm');
    });
    Route::get('logout', 'LoginController@logout');
});
