<?php
namespace TinyPixel\DigitalOcean\Spaces;

class Spaces
{
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
}
