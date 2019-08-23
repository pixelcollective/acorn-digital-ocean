<?php

namespace TinyPixel\Acorn\DigitalOcean;

use DigitalOceanV2\Entity\Account;
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
     * Digital Ocean
     * @var \GrahamCampbell\DigitalOcean\DigitalOceanManager
     */
    protected static $api;

    /**
     * Region API
     * @var
     */
    protected static $region;

    /**
     * Droplet API
     * @var
     */
    protected static $droplet;

    /**
     * Account API
     * @var
     */
    protected static $account;

    /**
     * Size API
     * @var
     */
    protected static $size;

    /**
     * API pluralizations
     * @var array
     */
    protected static $apiInflection = [
        'Regions'  => 'region',
        'Droplets' => 'droplet',
        'Accounts' => 'account',
        'Sizes'    => 'size',
    ];

    /**
     * Class constructor.
     *
     * @param \GrahamCampbell\DigitalOcean\DigitalOceanManager $do
     */
    public function __construct(DigitalOceanManager $do)
    {
        self::$api     = $do;
        self::$region  = $do->region();
        self::$droplet = $do->droplet();
        self::$account = $do->account();
        self::$size    = $do->size();
    }

    /**
     * Callable APIs.
     *
     * @param string $api
     * @param string $params
     */
    public function __call(string $apiCall, array $params = [])
    {
        if(strstr($apiCall, 'all')) {
            $api = explode('all', $apiCall)[1];

            if (!isset(self::$inflection[$api])) {
                return 'invalid api!';
            }

            $callableApi = self::$inflection[$api];

            return self::${$callableApi}->getAll();
        }
    }

    /**
     * Get account information.
     *
     * @return array
     */
    public function accountInfo() : Account
    {
        return self::$account->getUserInformation();
    }
}
