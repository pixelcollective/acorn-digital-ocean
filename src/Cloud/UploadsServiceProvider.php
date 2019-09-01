<?php

namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Collection;
use Roots\Acorn\ServiceProvider;
use TinyPixel\Acorn\Cloud\Hooks;
use TinyPixel\Acorn\Cloud\UploadsSync;

class UploadsServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->singleton('digitalocean.spaces', function ($app) {
            return (new Spaces());
        });

        $this->app->singleton('digitalocean.uploads.sync', function ($app) {
            return (new UploadsSync($app['filesystem'], $app))->init();
        });

        $this->app->singleton('digitalocean.uploads.hooks', function ($app) {
            return (new UploadsWordPressHooks($app, $app->make['digitalocean.spaces']));
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

        $this->app->make('digitalocean.uploads.hooks');
    }
}
