<?php

namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Collection;
use Roots\Acorn\ServiceProvider;
use TinyPixel\Acorn\DigitalOcean\SpacesStorage;

class SpacesStorageServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->singleton('digitalocean.spaces.storage', function ($app) {
            return (new SpacesStorage($app['filesystem'], $app))->init();
        });
    }

    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->app->make('digitalocean.spaces.storage');
    }
}
