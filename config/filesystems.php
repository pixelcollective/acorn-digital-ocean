<?php

use \Roots\env;

return [

    'spaces' => 'spaces',

    /*
    |--------------------------------------------------------------------------
    | Digital Ocean Spaces
    |--------------------------------------------------------------------------
    |
    | Provides Digital Ocean spaces filesystems support.
    |
    */

    'disks' => [
        'spaces' => [
            'driver'   => 's3',
            'bucket'   => env('DO_SPACES_BUCKET'),
            'endpoint' => env('DO_SPACES_ENDPOINT') ? env('DO_SPACES_ENDPOINT') : 'https://sfo2.digitaloceanspaces.com',
            'version'  => env('DO_SPACES_VERSION') ? env('DO_SPACES_VERSION') : 'latest|version',
            'region'   => env('DO_SPACES_REGION') ? env('DO_SPACES_REGION') : 'nyc3',
            'key'      => env('DO_SPACES_KEY'),
            'secret'   => env('DO_SPACES_SECRET'),
        ],
    ],
];
