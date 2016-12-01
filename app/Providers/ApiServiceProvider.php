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
     * @return void
     */
    public function boot(ResponseFactory $response)
    {
        $response->macro('api', function ($data = null, $code = null, $headers = [], $options = 0) {
            return new ApiResponse($data, $code, $headers, $options);
        });
    }
}
