<?php

namespace App\Support\Providers;

use App\Support\Tencent\XgPusher;
use Illuminate\Support\ServiceProvider;

class XgPusherServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(XgPusher::class, function () {
            return new XgPusher;
        });

        $this->app->alias(XgPusher::class, 'xgpusher');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            XgPusher::class,
            'xgpusher',
        ];
    }
}
