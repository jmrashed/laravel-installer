<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for audit logging during installation process
    |
    */

    'enabled' => env('INSTALLER_AUDIT_ENABLED', true),

    'channels' => [
        'audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/installer-audit.log'),
            'level' => 'info',
            'days' => 30,
        ],
    ],

    'events' => [
        'environment_saved',
        'database_connection_tested',
        'validation_failed',
        'rate_limit_exceeded',
        'backup_created',
        'installation_completed',
    ],

    'retention_days' => 90,

    'sensitive_fields' => [
        'database_password',
        'mail_password',
        'pusher_app_secret',
        'redis_password',
    ],
];