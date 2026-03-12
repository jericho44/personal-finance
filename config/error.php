<?php

return [
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'credit_card',
        'token',
        'api_key',
        'secret',
    ],

    'critical_exceptions' => [
        \PDOException::class,
        \ErrorException::class,
        // Add other critical error types
    ],

    'notification_channels' => [
        'slack' => env('ERROR_SLACK_WEBHOOK'),
        'email' => env('ERROR_EMAIL_ADDRESS'),
    ],

    'log_rate_limit' => 60, // seconds
];
