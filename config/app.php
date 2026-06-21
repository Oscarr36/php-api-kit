<?php
return [
    'name'       => $_ENV['APP_NAME']   ?? 'PHP API Kit',
    'env'        => $_ENV['APP_ENV']    ?? 'production',
    'debug'      => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url'        => $_ENV['APP_URL']    ?? 'http://localhost',

    'jwt_secret' => $_ENV['JWT_SECRET'] ?? 'change-this-to-a-random-secret-key',
    'jwt_ttl'    => (int) ($_ENV['JWT_TTL'] ?? 3600),

    'db' => [
        'host'     => $_ENV['DB_HOST']     ?? 'localhost',
        'port'     => $_ENV['DB_PORT']     ?? '3306',
        'name'     => $_ENV['DB_NAME']     ?? '',
        'user'     => $_ENV['DB_USER']     ?? '',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset'  => 'utf8mb4',
    ],

    'rate_limit' => [
        'max_requests' => (int) ($_ENV['RATE_LIMIT_MAX']    ?? 60),
        'window'       => (int) ($_ENV['RATE_LIMIT_WINDOW'] ?? 60),
    ],

    'cors' => [
        'origins' => array_map('trim', explode(',', $_ENV['CORS_ORIGINS'] ?? '*')),
    ],
];
