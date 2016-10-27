<?php

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');
