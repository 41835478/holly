<?php

namespace App\Support\Providers;

use App\Support\Http\ApiResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     */
    public function boot()
    {
        $this->extendResponses();
    }

    /**
     * Extend responses.
     */
    protected function extendResponses()
    {
        $response = $this->app->make(ResponseFactory::class);

        $response->macro('api', function (...$args) {
            return new ApiResponse(...$args);
        });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        array_map([$this->app, 'register'], $this->getServiceProviders());
    }

    /**
     * Get service providers to be registered.
     *
     * @return array
     */
    protected function getServiceProviders()
    {
        $services = [
            AppConfigServiceProvider::class,
            CaptchaServiceProvider::class,
            ClientServiceProvider::class,
            OptimusServiceProvider::class,
            XgPusherServiceProvider::class,
        ];

        if ($this->app->runningInConsole()) {
            array_push(
                $services,
                ConsoleServiceProvider::class,
                \BackupManager\Laravel\Laravel5ServiceProvider::class
            );
        }

        if ($this->app->isLocal()) {
            array_push(
                $services,
                \Barryvdh\Debugbar\ServiceProvider::class,
                \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class
            );
        }

        return $services;
    }
}
