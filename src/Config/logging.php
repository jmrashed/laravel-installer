<?php

return [
    'channels' => [
        'installer' => [
            'driver' => 'daily',
            'path' => storage_path('logs/installer.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/installer-security.log'),
            'level' => 'warning',
            'days' => 30,
        ],

        'audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/installer-audit.log'),
            'level' => 'info',
            'days' => 90,
        ],

        'performance' => [
            'driver' => 'daily',
            'path' => storage_path('logs/installer-performance.log'),
            'level' => 'info',
            'days' => 7,
        ]
    ],

    'log_operations' => [
        'environment_save',
        'database_connection',
        'file_operations',
        'validation_errors',
        'security_events',
        'performance_metrics'
    ]
];