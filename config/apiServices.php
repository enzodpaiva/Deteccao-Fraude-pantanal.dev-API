<?php

return [

    // BITRIX CRM
    'crm' => [
        'client' => [
            'base_uri' => env('BITRIX_DOMAIN'),
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

    //PITZI
    'pitzi' => [
        'client' => [
            'base_uri' => env("PITZI_API_URL"),
            'usuario' => env("PITZI_API_USER"),
            'password' => env("PITZI_API_PASSWORD"),
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

    // API Support
    'service_support' => [
        'client' => [
            'base_uri' => env('SERVICE_SUPPORT_API', 'https://api.beta.support.vertexdigital.co'),
        ],
    ],
];
