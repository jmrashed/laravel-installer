<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CacheQueueManager
{
    public static function clearAllCaches()
    {
        $results = [];
        
        $cacheCommands = [
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'View cache',
            'cache:clear' => 'Application cache'
        ];

        foreach ($cacheCommands as $command => $description) {
            try {
                Artisan::call($command);
                $results[$command] = ['success' => true, 'description' => $description];
            } catch (\Exception $e) {
                $results[$command] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        // Clear OPcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
            $results['opcache'] = ['success' => true, 'description' => 'OPcache'];
        }

        return $results;
    }

    public static function setupQueues($config)
    {
        $driver = $config['queue_driver'] ?? 'sync';
        $connection = $config['queue_connection'] ?? 'default';
        
        $envUpdates = [
            'QUEUE_CONNECTION' => $driver
        ];

        if ($driver === 'redis') {
            $envUpdates = array_merge($envUpdates, [
                'REDIS_HOST' => $config['redis_host'] ?? '127.0.0.1',
                'REDIS_PASSWORD' => $config['redis_password'] ?? 'null',
                'REDIS_PORT' => $config['redis_port'] ?? '6379'
            ]);
        }

        self::updateEnvFile($envUpdates);
        
        // Create queue tables if using database driver
        if ($driver === 'database') {
            try {
                Artisan::call('queue:table');
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                LogManager::logError('Queue table creation failed', $e);
            }
        }

        return ['success' => true, 'driver' => $driver];
    }

    public static function setupScheduler()
    {
        $cronEntry = '* * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1';
        
        // Create scheduler info file
        $schedulerPath = storage_path('installer/scheduler.txt');
        File::ensureDirectoryExists(dirname($schedulerPath));
        File::put($schedulerPath, $cronEntry);

        return [
            'success' => true,
            'cron_entry' => $cronEntry,
            'info_file' => $schedulerPath
        ];
    }

    public static function optimizeApplication()
    {
        $results = [];
        
        $optimizeCommands = [
            'config:cache' => 'Cache configuration',
            'route:cache' => 'Cache routes',
            'view:cache' => 'Cache views'
        ];

        foreach ($optimizeCommands as $command => $description) {
            try {
                Artisan::call($command);
                $results[$command] = ['success' => true, 'description' => $description];
            } catch (\Exception $e) {
                $results[$command] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    private static function updateEnvFile($updates)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envPath, $envContent);
    }
}