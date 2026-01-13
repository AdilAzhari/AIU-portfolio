<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | IPFS Integration Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable IPFS file storage
    |
    */
    'enabled' => env('IPFS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | IPFS Gateway & API
    |--------------------------------------------------------------------------
    |
    | Local IPFS node endpoints
    | Start local node: ipfs daemon
    |
    */
    'gateway' => env('IPFS_GATEWAY', 'http://127.0.0.1:8080'),
    'api' => env('IPFS_API', 'http://127.0.0.1:5001'),

    /*
    |--------------------------------------------------------------------------
    | Pinata Configuration (FREE tier: 1GB)
    |--------------------------------------------------------------------------
    |
    | Pinata is a pinning service for IPFS
    | Sign up: https://pinata.cloud (FREE)
    |
    */
    'pinata' => [
        'enabled' => env('PINATA_ENABLED', false),
        'api_key' => env('PINATA_API_KEY'),
        'secret_key' => env('PINATA_SECRET_KEY'),
        'jwt' => env('PINATA_JWT'),
        'gateway' => 'https://gateway.pinata.cloud/ipfs/',
        'api_url' => 'https://api.pinata.cloud',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Pin Service
    |--------------------------------------------------------------------------
    |
    | Choose: 'local' or 'pinata'
    |
    */
    'default' => env('IPFS_DEFAULT_SERVICE', 'local'),
];