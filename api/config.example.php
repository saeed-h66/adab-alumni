<?php
return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=adab_alumni;charset=utf8mb4',
        'user' => 'DB_USER',
        'password' => 'DB_PASSWORD',
    ],
    'otp' => [
        'ttl_seconds' => 120,
        'max_attempts' => 5,
    ],
    'sms' => [
        'provider' => 'YOUR_SMS_PROVIDER',
        'api_key' => 'SET_ON_SERVER_ONLY',
    ],
];
