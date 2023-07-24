<?php

return [
    'default' => env('DB_CONNECTION', 'mongodb'),
    'migrations' => 'migrations',

    'connections' => [
        'mongodb' => [
            'driver' => 'mongodb',
            'host' => [
                env('DB_HOST', 'localhost'),
                env('DB_SHARD01', 'localhost'),
                env('DB_SHARD02', 'localhost'),
            ],
            'port' => env('DB_PORT', 27017),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'database' => env('DB_DATABASE', ''),
            'options' => [
                'database' => env('DB_AUTHDATABASE', 'admin'),
                'replicaSet' => env('DB_REPLICASET', ''),
                'ssl' => true,
            ],
        ],

        'mongodb-local' => [
            'driver' => 'mongodb',
            'host' => [
                env('DB_HOST', 'mongo'),
                env('DB_SHARD01', 'mongo'),
                env('DB_SHARD02', 'mongo'),
            ],
            'port' => env('DB_PORT', 27017),
            // 'username' => env('DB_USERNAME', ''),
            // 'password' => env('DB_PASSWORD', ''),
            'database' => env('DB_DATABASE', 'pitzi-online'),
            'options' => [
                'database' => env('DB_AUTHDATABASE', 'admin'),
            ],
        ],

        'mongodb-test' => [
            'driver' => 'mongodb',
            'host' => [
                env('DB_HOST', 'mongo'),
                env('DB_SHARD01', 'mongo'),
                env('DB_SHARD02', 'mongo'),
            ],
            'port' => env('DB_PORT', 27017),
            'database' => 'pitzi-online-test',
            //'username' => env('DB_USERNAME', ''),
            //'password' => env('DB_PASSWORD', ''),
            'options' => [
                'database' => env('DB_AUTHDATABASE', 'admin'),
            ],
        ],
    ],
];
