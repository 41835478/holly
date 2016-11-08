<?php

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', 'Auth\LoginController@showLoginForm');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});
Route::get('logout', 'Auth\LoginController@logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index');
    Route::get('log', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
