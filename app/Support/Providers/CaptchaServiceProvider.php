<?php

namespace App\Support\Providers;

use Mews\Captcha\CaptchaServiceProvider as ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Override the Captcha service provider to remove the default routing.
     */
    public function boot()
    {
        $this->publishes([
            base_path('vendor/mews/captcha/config/captcha.php') => config_path('captcha.php'),
        ], 'config');

        $this->app['validator']->extend('captcha', function ($attribute, $value, $parameters) {
            return captcha_check($value);
        });
    }
}
