<?php

namespace App\Providers;

use App\Http\ApiResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     */
    public function boot(ResponseFactory $response)
    {
        $response->macro('api', function (...$args) {
            return new ApiResponse(...$args);
        });
    }
}
