<?php

namespace App\Providers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use App\Http\ApiResponse;

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
