<?php
namespace Ehesp\SteamLogin\Laravel;

use Illuminate\Support\ServiceProvider;
use Ehesp\SteamLogin\SteamLogin;

class SteamLoginServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('steamlogin', function($app) {
            return new SteamLogin();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('steamlogin');
    }
}
