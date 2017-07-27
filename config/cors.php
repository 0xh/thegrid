<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => false,
    'allowedOrigins' => [
        'http://api.thegrid.com',
        'https://api.thegrid.com',
        'http://dev.thegrid.com',
        'https://dev.thegrid.com',
        'http://10.0.0.10:8080',
        'http://127.0.0.1:8080',
        'http://127.0.0.1',
        'https://127.0.0.1',
        'https://thegridpolymer.azurewebsites.net',
        'http://thegridpolymer.azurewebsites.net',
    ],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,
];
