<?php

namespace Mcprohosting\Flywheel;

use Illuminate\Support\ServiceProvider;

class FlywheelServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('flywheel', function($app) {
            return new Factory(new CallHandler($app['cache']));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('flywheel');
    }

}
