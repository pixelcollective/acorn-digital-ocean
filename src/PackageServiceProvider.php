<?php

namespace TinyPixel\Acorn\DigitalOcean;

use TinyPixel\Acorn\DigitalOcean\DigitalOcean;
use Roots\Acorn\ServiceProvider;

/**
 * Business logics
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register application services
     */
    public function register()
    {
        $this->app->singleton('digitalocean.wp', function ($app) {
            return new DigitalOcean($app->make('digitalocean'));
        });
    }

    /**
     * Boot application services.
     */
    public function boot()
    {
        $do = $this->app->make('digitalocean.wp');
    }
}
