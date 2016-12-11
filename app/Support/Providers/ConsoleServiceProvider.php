<?php

namespace App\Support\Providers;

use App\Support\Console\Commands\ApiTokenKey;
use App\Support\Console\Commands\AssetsVersion;
use App\Support\Console\Commands\Int2stringCharacters;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
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
        $this->app->singleton('command.api.token-key', function () {
            return new ApiTokenKey;
        });

        $this->app->singleton('command.assets.version', function () {
            return new AssetsVersion;
        });

        $this->app->singleton('command.int2string.characters', function () {
            return new Int2stringCharacters;
        });

        $this->commands(
            'command.api.token-key',
            'command.assets.version',
            'command.int2string.characters'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.api.token-key',
            'command.assets.version',
            'command.int2string.characters',
        ];
    }
}
