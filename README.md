# Digital Ocean Filesystem Adapter for Sage 10 and Clover

## Install

- `composer require tiny-pixel/digital-ocean-service-provider`.

## Usage

1. Add `\TinyPixel\DigitalOceanServiceProvider::class` to your `config/app.php` array.

2. Add to your `config/filesystems.php` array and make sure the specified values are available in your environment:

```php
'spaces' => [
    'driver'   => 's3',
    'key'      => env('DO_SPACES_KEY'),
    'secret'   => env('DO_SPACES_SECRET'),
    'endpoint' => env('DO_SPACES_ENDPOINT'),
    'version'  => env('DO_SPACES_VERSION', 'latest|version'),
    'region'   => env('DO_SPACES_REGION'),
    'bucket'   => env('DO_SPACES_BUCKET'),
],
```
Congrats! Get to puttin'!

See the [Laravel File Storage documentation](https://laravel.com/docs/5.8/filesystem) for implemenation examples and usage documentation.