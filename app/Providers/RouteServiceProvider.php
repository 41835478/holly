<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->pattern('id', '[0-9]+');

        $this->bind('admin_user', function ($value) {
            return \App\Models\AdminUser::find($value);
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // Defines all routes in format: `identifier => attributes`.
        // If there is no "namespace" in attributes, the default namespace will be `$this->namespace.'\\'.studly_case($identifier)`.
        // The routes definitions will be placed in file "routes/{$identifer}.php".
        $routes = [
            'site' => [
                'domain' => config('app.domain.site'),
                'middleware' => 'web',
            ],
            'admin' => [
                'domain' => config('app.domain.admin'),
                'middleware' => 'web',
            ],
            'api' => [
                'domain' => config('app.domain.api'),
                'middleware' => 'api',
            ],
            'api-web' => [
                'domain' => config('app.domain.site'),
                'prefix' => 'api',
                'namespace' => 'Site',
                'middleware' => ['web', 'api.client'],
            ],
        ];

        foreach ($routes as $identifier => $attributes) {
            $attributes['namespace'] = $this->namespace.'\\'.
                studly_case(array_get($attributes, 'namespace', $identifier));

            Route::group(
                $attributes,
                function ($router) use ($identifier) {
                    require base_path('routes/'.$identifier.'.php');
                }
            );
        }
    }
}
