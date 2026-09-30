<?php
// CCALM configuration.
// For production, move credentials outside the public web root when possible.
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'ccalm',
        'user' => 'kaloqn',
        'pass' => '123456',
        'charset' => 'utf8mb4',
    ],
    'admin_password' => '123456',
    'seat_capacity' => 8,
];
