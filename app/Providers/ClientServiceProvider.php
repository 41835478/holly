<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Client;
use Jenssegers\Agent\Agent;

class ClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     */
    public function boot()
    {
        $this->app['view']->share('client', $this->app['client']);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerAgent();

        $this->registerClient();
    }

    /**
     * Register the Agent.
     */
    protected function registerAgent()
    {
        if (! $this->app->bound('agent')) {
            $this->app->singleton('agent', function () {
                return new Agent;
            });

            $this->app->alias('agent', Agent::class);
        }
    }

    /**
     * Register the Client.
     */
    protected function registerClient()
    {
        $this->app->singleton('client', function () {
            return new Client;
        });

        $this->app->alias('client', Client::class);
    }
}
