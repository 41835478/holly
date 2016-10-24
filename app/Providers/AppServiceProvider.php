<?php

namespace App\Providers;

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
        parent::boot();
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
}
