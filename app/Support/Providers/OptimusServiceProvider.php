<?php

namespace App\Support\Providers;

use Jenssegers\Optimus\Optimus;
use Illuminate\Support\ServiceProvider;

class OptimusServiceProvider extends ServiceProvider
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
        $this->app->singleton(Optimus::class, function ($app) {
            $config = $app['config']['optimus'];

            return new Optimus($config['prime'], $config['inverse'], $config['random']);
        });

        $this->app->alias(Optimus::class, 'optimus');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Optimus::class, 'optimus'];
    }
}
