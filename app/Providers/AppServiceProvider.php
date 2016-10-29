<?php

namespace App\Providers;

use Carbon\Carbon;
use Holly\Providers\AppServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        if ($this->app['config']['app.debug']) {
            $this->registerServicesForDebugging();
        }

        if ($this->app->runningInConsole()) {
            $this->registerServicesForConsole();
        }

        if (is_domain('admin')) {
            $this->registerServicesForAdmin();
        }

        if (is_domain('api')) {
            $this->app->rebinding('request', function ($app, $request) {
                $this->hackForApiRequest($request);
            });
        }
    }

    /**
     * Register services for debugging.
     */
    protected function registerServicesForDebugging()
    {
        $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }

    /**
     * Register services for console.
     */
    protected function registerServicesForConsole()
    {
        $this->app->register(\BackupManager\Laravel\Laravel5ServiceProvider::class);
    }

    /**
     * Register services for admin.
     */
    protected function registerServicesForAdmin()
    {
        $this->app->register(\Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class);
    }

    /**
     * Hack the current Request instance for adding "Accept: application/json" header,
     * to make `$request->expectsJson()` working for API requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function hackForApiRequest($request)
    {
        if (! str_contains(($accept = $request->headers->get('Accept')), ['/json', '+json'])) {
            $accept = 'application/json'.(empty($accept) ? '' : ',').$accept;

            $request->headers->set('Accept', $accept);
            $request->server->set('HTTP_ACCEPT', $accept);
        }
    }
}
