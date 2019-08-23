<?php

use \Roots\env;

return [
    'spaces' => [
        'driver'   => 's3',
        'key'      => env('DO_SPACES_KEY'),
        'secret'   => env('DO_SPACES_SECRET'),
        'bucket'   => env('DO_SPACES_BUCKET'),
        'endpoint' => env('DO_SPACES_ENDPOINT', 'https://sfo2.digitaloceanspaces.com'),
        'version'  => env('DO_SPACES_VERSION', 'latest|version'),
        'region'   => env('DO_SPACES_REGION', 'nyc3'),
    ],
];
