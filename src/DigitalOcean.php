<?php

namespace TinyPixel\Acorn\DigitalOcean;

use GrahamCampbell\DigitalOcean\DigitalOceanManager;

/**
 * Digital Ocean
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class DigitalOcean
{
    /**
     * DigitalOcean
     */
    protected $digitalocean;

    /**
     * Constructor.
     *
     * @param \GrahamCampbell\DigitalOcean\DigitalOceanManager $do
     */
    public function __construct(DigitalOceanManager $do)
    {
        $this->digitalocean = $digitalocean;
    }

    /**
     * Method
     *
     * @return void
     */
    public function __invoke() : void
    {
        return $this->digitalocean->region()->getAll();
    }
}
