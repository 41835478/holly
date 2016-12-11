<?php

namespace App\Support\Providers;

use Illuminate\Support\ServiceProvider;

class AppConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        if (! $this->app->configurationIsCached()) {
            $this->configureDefaults();
        }

        $this->configureForCurrentRequest();
    }

    /**
     * Configure app defaults.
     */
    protected function configureDefaults()
    {
        $config = $this->app['config'];

        // Append "app.domains"
        $config['app.domains'] = array_map(function ($value) {
            if (is_string($domain = parse_url($value, PHP_URL_HOST))) {
                if (str_contains($domain, '.')) {
                    return $domain;
                }
            }
        }, $config['support.url']);

        // Set "mail.from.name"
        if ($config['mail.from.name'] == 'Example') {
            $config['mail.from.name'] = $config['app.name'];
        }
    }

    /**
     * Configure app for the current request.
     */
    protected function configureForCurrentRequest()
    {
        $config = $this->app['config'];
        $request = $this->app['request'];

        $identifier = array_search($request->getHost(), $config['app.domains']);

        // Configure the cookie domain
        if (! is_null($identifier) && $config->has('support.cookie_domain.'.$identifier)) {
            $config['session.domain'] = $config['support.cookie_domain.'.$identifier];
        }

        // Configure the auth defaults
        if (! is_null($identifier) && is_array($auth = $config['support.auth.'.$identifier])) {
            $config['auth.defaults'] = $auth;
        }
    }
}
