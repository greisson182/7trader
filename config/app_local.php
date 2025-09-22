<?php
return [
    'debug' => true,
    'Security' => [
        'salt' => env('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi'),
    ],
    'Datasources' => [
        'default' => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'backtest_db',
            'url' => env('DATABASE_URL', null),
        ],
        'test' => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'backtest_test',
        ],
    ],
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];