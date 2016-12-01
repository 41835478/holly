<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->isLocal()) {
            $this->registerServices($this->getProvidersForLocalEnvironment());
        }

        if ($this->app->runningInConsole()) {
            $this->registerServices($this->getProvidersForConsole());
        }

        if (is_domain('admin')) {
            $this->registerServices($this->getProvidersForAdminSite());
        }

        if (is_domain('api')) {
            $this->addAcceptableJsonType();
        }
    }

    /**
     * Register services.
     *
     * @param  string|array  $services
     * @return void
     */
    protected function registerServices($services)
    {
        foreach ((array) $services as $value) {
            $this->app->register($value);
        }
    }

    /**
     * Get the services provided for local environment.
     *
     * @return array
     */
    protected function getProvidersForLocalEnvironment()
    {
        return [
            'Barryvdh\Debugbar\ServiceProvider',
            'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
        ];
    }

    /**
     * Get the services provided for console.
     *
     * @return array
     */
    protected function getProvidersForConsole()
    {
        return [
            'BackupManager\Laravel\Laravel5ServiceProvider',
        ];
    }

    /**
     * Get the services provided for admin site.
     *
     * @return array
     */
    protected function getProvidersForAdminSite()
    {
        return [
            'Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider',
        ];
    }

    /**
     * Add "application/json" to the "Accept" header for the current request,
     * it will make `$request->expectsJson()` return true.
     */
    protected function addAcceptableJsonType()
    {
        $this->app->rebinding('request', function ($app, $request) {
            if (! str_contains(($accept = $request->header('Accept')), ['/json', '+json'])) {
                $accept = rtrim('application/json,'.$accept, ',');

                $request->headers->set('Accept', $accept);
                $request->server->set('HTTP_ACCEPT', $accept);
            }
        });
    }
}
