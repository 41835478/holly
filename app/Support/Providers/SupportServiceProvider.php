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
        $this->app->register(CaptchaServiceProvider::class);
        $this->app->register(ClientServiceProvider::class);
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(OptimusServiceProvider::class);
    }
}
