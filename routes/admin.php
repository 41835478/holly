<?php

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');

Route::group(['namespace' => 'Auth'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('login', 'LoginController@showLoginForm');
        Route::post('login', 'LoginController@login');
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm');
        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm');
        Route::post('password/reset', 'ResetPasswordController@reset');
    });
    Route::get('logout', 'LoginController@logout');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index');
    Route::get('log', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
        Route::get('users', 'UserController@users')
            ->middleware('can:manager,App\Models\AdminUser');
        Route::get('user/create', 'UserController@showCreateUser')
            ->middleware('can:create,App\Models\AdminUser');
        Route::post('user/create', 'UserController@createUser')
            ->middleware('can:create,App\Models\AdminUser');
        Route::post('user/delete/{admin_user}', 'UserController@deleteUser')
            ->middleware('can:delete,admin_user');
        Route::get('profile/{admin_user?}', 'ProfileController@show');
        Route::post('profile/{admin_user?}', 'ProfileController@edit');
    });

    Route::get('user', 'UserController@index');
    Route::get('device', 'DeviceController@index');
    Route::get('feedback', 'FeedbackController@index');
});
