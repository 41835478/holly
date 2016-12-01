<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->app->configurationIsCached()) {
            $this->configureDefaults();
        }

        $this->configureForRequest($this->app['request']);
    }

    /**
     * Configure defaults.
     */
    protected function configureDefaults()
    {
        $this->appendAppDomainsConfig();

        $this->setMailConfig();
    }

    /**
     * Append "app.domains" config.
     */
    protected function appendAppDomainsConfig()
    {
        $this->app['config']['app.domains'] = array_map(function ($value) {
            if (is_string($domain = parse_url($value, PHP_URL_HOST))) {
                if (str_contains($domain, '.')) {
                    return $domain;
                }
            }
        }, $this->app['config']['holly.url']);
    }

    /**
     * Set "mail" config.
     */
    protected function setMailConfig()
    {
        if (str_contains($username = $this->app['config']['mail.username'], '@') &&
            $this->app['config']['mail.from.address'] == 'hello@example.com') {
            $this->app['config']['mail.from.address'] = $username;
        }

        if ($this->app['config']['mail.from.name'] == 'Example') {
            $this->app['config']['mail.from.name'] = $this->app['config']['app.name'];
        }
    }

    /**
     * Configure app for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function configureForRequest($request)
    {
        $domain = $request->getHost();
        $identifier = array_search($domain, $this->app['config']['app.domains']);

        // Configure the cookie domain
        if (! is_null($identifier) && $this->app['config']->has('holly.cookie_domain.'.$identifier)) {
            $this->app['config']['session.domain'] = $this->app['config']['holly.cookie_domain.'.$identifier];
        }

        // Configure the auth defaults
        if (! is_null($identifier) && is_array($auth = $this->app['config']['holly.auth.'.$identifier])) {
            $this->app['config']['auth.defaults'] = $auth;
        }
    }
}
