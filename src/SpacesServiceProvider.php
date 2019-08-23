<?php

namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Roots\Acorn\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class SpacesServiceProvider extends ServiceProvider
{
    /**
     * Register application services
     */
    public function register()
    {
    }

    /**
     * Boot application services.
     */
    public function boot()
    {
        Storage::extend('spaces', function ($app, $config) {
            $this->configureSpaces($app, $config);
        });
    }

    /**
     * Configure spaces.
     *
     * @param  Application $app
     * @param  array       $config
     *
     * @return void
     */
    protected function configureSpaces($app, $config)
    {
        $spacesAdapter = new AwsS3Adapter(
            new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region'   => $config['region'],
                'version'  => $config['version'],
                'endpoint' => $config['region'],
            ])
        );

        return new Filesystem($spacesAdapter, $config['bucket']);
    }
}
