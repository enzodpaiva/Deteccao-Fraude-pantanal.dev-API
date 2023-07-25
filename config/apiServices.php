<?php

return [
    //Server
    'server' => [
        'client' => [
            'base_uri' => env('SERVER_URL'),
        ],
        'options' => [
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
            ],
        ],
    ],
];
