<?php
namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Roots\Acorn\ServiceProvider;

class SpacesServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register() : void
    {
        $configSource = realpath($raw = __DIR__ . '/../config/filesystems.php') ?: $raw;

        $this->mergeConfigFrom($configSource, 'filesystems');
    }

    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot() : void
    {
        Storage::extend('spaces', function ($app, $config) {
            return new Filesystem($this->spacesAdapter($config, $config['bucket']));
        });
    }

    /**
     * Spaces adapter.
     *
     * @param  array $config
     * @return League\Flysystem\AwsS3v3\AwsS3Adapter
     */
    protected function spacesAdapter(array $config, string $bucket) : AwsS3Adapter
    {
        return new AwsS3Adapter(
            $this->spacesClient($config),
            $bucket
        );
    }

    /**
     * Spaces client.
     *
     * @param  array $config
     * @return Aws\S3\S3Client
     */
    protected function spacesClient(array $config) : S3Client
    {
        return new S3Client([
            'credentials' => $config['credentials'],
            'region' => $config['region'],
            'version' => $config['version'],
            'endpoint' => $config['endpoint'],
        ]);
    }
}
