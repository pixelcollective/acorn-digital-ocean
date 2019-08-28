<?php
namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\FilesystemManager as Filesystem;
use Roots\Acorn\Application;

class SpacesStorage
{
    /** @var \Illuminate\Support\Collection */
    public static $locations;

    /** @var object */
    public static $stores;

    /** @var object */
    public static $disks;

    /** @var string */
    public static $storeDir = '';

    /**
     * Class constructor.
     *
     * @param  \League\Flysystem\Filesystem $filesystem
     * @param  \Roots\Acorn\Application     $app
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem, Application $app)
    {
        $appDir = $app['config']->get(
            'filesystems.disk.local.root'
        );

        $uploadsDir = "{$appDir}/uploads";

        self::$stores = (object) [
            'local'  => $uploadsDir,
            'remote' => self::$storeDir,
        ];

        self::$disks = (object) [
            'local'  => $filesystem->disk('local'),
            'remote' => $filesystem->disk('spaces'),
        ];

        $this->fs = (object) [];
    }

    /**
     * Initializes class.
     *
     * @return void
     */
    public function init() : void
    {
        Collection::make(self::$disks)->each(function ($contents, $disk) {
            $this->listDisk($disk);
        });

        $this->processDirectories();

        $this->processFiles();

        /**
         * Todo: complete WordPress integration
         */
        // add_action('plugins_loaded', [$this, 'hooks']);
    }

    /**
     * Makes collections of files and directories on
     * local and remote disks.
     *
     * @return void
     */
    protected function listDisk($disk)
    {
        $this->fs->$disk = (object) [
            'files' => Collection::make(
                self::$disks->$disk->allFiles(self::$stores->$disk)
            ),
            'dirs' => Collection::make(
                self::$disks->$disk->allDirectories(self::$stores->$disk)
            ),
        ];
    }

    /**
     * Process resources: directories
     *
     * @return void
     */
    protected function processDirectories()
    {
        $this->newDirs = $this->fs->local->dirs->except(
            $this->fs->remote->dirs
        )->each(function ($dir) {
            $this->mkdir($this->destination($dir));
        });
    }

    /**
     * Process resources
     *
     * @return void
     */
    protected function processFiles()
    {
        $this->newFiles = $this->fs->local->files->except(
            $this->fs->remote->files
        )->each(function ($file) {
            $this->touch($this->destination($file), $this->localFileContents($file));
        });
    }

    /**
     * Local file contents
     *
     * @return void
     */
    protected function localFileContents($file)
    {
        return file_get_contents(self::$disks->local->path($file));
    }

    /**
     * Write public file.
     *
     * @param  string
     * @return void
     */
    protected function touch(string $destination, string $file)
    {
        self::$disks->remote->put($destination, $file, 'public');
        dump(["[mock] creating remote file: {$file}"]);
    }

    /**
     * Write public dir.
     *
     * @param  string
     * @return void
     */
    protected function mkdir(string $dir)
    {
        self::$disks->remote->makeDirectory($dir, 'public');
        dump(["[mock] creating remote dir: {$dir}"]);
    }

    /**
     * Destination locator.
     *
     * @param  string $file
     * @return string
     */
    protected function destination(string $file)
    {
        return self::$stores->remote . '/' . $file;
    }

    /**
     * Hooks.
     *
     * @return void
     */
    public function hoooks()
    {
        add_filter('upload_dir', [$this, 'filterUploadDir']);
        add_filter('wp_image_editors', [$this, 'filterEditors'], 9);
        add_action('delete_attachment', [$this, 'deleteAttachmentFiles']);
        add_filter('wp_read_image_metadata', [$this, 'wpFilterReadImageMetadata'], 10, 2);
        add_filter('wp_resource_hints', [$this, 'wpFilterResourceHints'], 10, 2);
        add_action('wp_handle_sideload_prefilter', [$this, 'filterSideload']);

        remove_filter('admin_notices', 'wpthumb_errors');
    }
}
