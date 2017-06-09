<?php

namespace App\Support\Datatables;

use App\Support\Datatables\Html\Builder as HtmlBuilder;
use Illuminate\Support\ServiceProvider;

/**
 * Datatables Customization.
 *
 * You must register this provider **after** the original Yajra providers.
 */
class DatatablesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Replace html builder binding.
        // See Yajra\Datatables\HtmlServiceProvider
        $this->app->bind('datatables.html', HtmlBuilder::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['datatables.html'];
    }
}
