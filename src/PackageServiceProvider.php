<?php

namespace TinyPixel\Acorn\DigitalOcean;

use TinyPixel\Acorn\DigitalOcean\DigitalOcean;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register application services
     */
    public function register()
    {
        $this->app->singleton('digitalocean.acorn', function () {
            return new DigitalOcean();
        });
    }

    /**
     * Boot application services.
     */
    public function boot()
    {
        $digitalOcean = $this->app->make('digitalocean.acorn');
        $digitalOcean();

        dd($this);
    }
}
