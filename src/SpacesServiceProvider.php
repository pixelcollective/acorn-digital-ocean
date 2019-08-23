<?php

namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Roots\Acorn\ServiceProvider;

class SpacesServiceProvider extends ServiceProvider
{
    /**
     * Register application services
     */
    public function register()
    {
    }

    public function boot()
    {
        $this->app['storage']->extend('spaces', [$this, 'configureSpaces']);

        dd($this->app['storage']);
    }

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
