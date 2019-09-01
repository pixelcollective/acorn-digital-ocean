<?php
namespace TinyPixel\Acorn\DigitalOcean;

use Aws\S3\S3Client;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\FilesystemManager as Filesystem;
use Roots\Acorn\Application;

class Sync
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
    public function __construct(LocalFilesystem $localFilesystem, EdgeProvider $edgeFilesystem, Application $app)
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
            'local'  => $localFilesystem,
            'remote' => $edgeFilesystem,
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
        $writeDirs = $this->fs->local->dirs->except(
            $this->fs->remote->dirs
        );

        $writeDirs->each(function ($dir) {
            $this->makeDirectory($dir);
        });
    }

    /**
     * Process resources
     *
     * @return void
     */
    protected function processFiles()
    {
        $new = $this->fs->local->files->except(
            $this->fs->remote->files
        );

        $new->each(function ($file) {
            $this->writeFile($file, $this->localFileContents($file));
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
    protected function writeFile(string $fileName, string $fileContents)
    {
        self::$disks->remote->put($fileName, $fileContents, 'public');
    }

    /**
     * Write public dir.
     *
     * @param  string
     * @return void
     */
    protected function makeDirectory(string $dir)
    {
        self::$disks->remote->makeDirectory($dir, 'public');
    }
}
