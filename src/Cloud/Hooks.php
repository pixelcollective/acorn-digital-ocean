<?php
namespace TinyPixel\Acorn\DigitalOcean;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\FilesystemManager as Filesystem;

class SpacesStorageWordPressHooks
{
    /** @var \Illuminate\Support\Collection */
    public static $filters;

    /** @var \Illuminate\Support\Collection */
    public static $removeFilters;

    /** @var \Illuminate\Support\Collection */
    public static $actions;

    /** @var \TinyPixel\Acorn\DigitalOcean\Spaces */
    public static $spaces;

    /**
     * Class constructor.
     *
     * @param  \League\Flysystem\Filesystem $filesystem
     * @param  \Roots\Acorn\Application     $app
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem, Application $app, Spaces $spaces)
    {
        self::$spaces = new $spaces();

        self::$hooks  = Collection::make([
            'add_filter' => Collection::make([
                'upload_dir' => [
                    'class'     => 'this',
                    'handler'   => 'filterUploadDir',
                    'arguments' => [],
                ],
                'wp_image_editors' => [
                    'class'     => 'this',
                    'handler'   => 'filterEditors',
                    'arguments' => [9, 2],
                ],
                'wp_resources_hints' => [
                    'class'     => 'this',
                    'handler'   => 'filterHints',
                    'arguments' => [10, 2],
                ],
                'wp_read_image_metadata' => [
                    'class'     => 'this',
                    'handler'   => 'filterImageMeta',
                    'arguments' => [10, 2],
                ],
            ]),
            'add_action' => Collection::make([
                'delete_attachment' => [
                    'class'     => 'this',
                    'handler'   => 'deleteAttachment',
                    'arguments' => [],
                ],
                'wp_handle_sideload_prefilter' => [
                    'class'     => 'this',
                    'handler'   => 'sideloadErrors',
                    'arguments' => [],
                ],
            ]),
            'remove_filter' => Collection::make([
                'admin_notices' => [
                    'class'     => 'this',
                    'handler'   => 'noticeErrors',
                    'arguments' => [],
                ],
            ]),
        ]);
    }

    /**
     * Initializes class.
     *
     * @return void
     */
    public function init() : void
    {
        $this->runWordPressHooks();
    }

    /**
     * Run WordPress hooks.
     *
     * @return void
     */
    public function runWordPressHooks()
    {
        self::$hooks->each(function ($handlers, $callable) {
            $handlers->each(function ($callback, $wordPressHookTag) use ($callable) {
                $callable($wordPressHookTag, [
                    $callback['class'],
                    $callback['handler']
                ], ...$callback['arguments']);
            });
        });
    }

    /**
     * Filter upload directory.
     *
     * @param  array $dirs
     * @return
     */
    protected function filterUploadDir(array $dirs)
    {
        $dirs['path']    = str_replace(WP_CONTENT_DIR, 'https://' . $this->bucket(), $dirs['path']);
        $dirs['basedir'] = str_replace(WP_CONTENT_DIR, 'https://' . $this->bucket(), $dirs['basedir']);

        $dirs['url']     = str_replace('s3://' . $this->bucket, $this->get_s3_url(), $dirs['path']);
        $dirs['baseurl'] = str_replace('s3://' . $this->bucket, $this->get_s3_url(), $dirs['basedir']);

        return $dirs;
    }
}
