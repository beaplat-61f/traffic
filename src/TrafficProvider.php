<?php

namespace Beaplat\Traffic;

use Illuminate\Support\ServiceProvider;

class TrafficProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Copy the migration and config file to project
        $this->publishes([
            __DIR__ . '/migrations' => database_path('migrations'),
            __DIR__ . '/config/traffic.php' => config_path('traffic.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('traffic', function () {
           return new TrafficHelper();
        });
    }
}
