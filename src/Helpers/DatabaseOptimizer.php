<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseOptimizer
{
    public static function optimizeForLargeDatabase()
    {
        $config = [
            'mysql' => [
                'SET SESSION sql_mode = ""',
                'SET SESSION foreign_key_checks = 0',
                'SET SESSION unique_checks = 0',
                'SET SESSION autocommit = 0'
            ],
            'pgsql' => [
                'SET synchronous_commit = off',
                'SET checkpoint_segments = 32',
                'SET wal_buffers = 16MB'
            ]
        ];

        $driver = config('database.default');
        $statements = $config[$driver] ?? [];

        foreach ($statements as $statement) {
            try {
                DB::statement($statement);
            } catch (\Exception $e) {
                LogManager::logError('Database optimization failed', $e, ['statement' => $statement]);
            }
        }
    }

    public static function runMigrationsInBatches($batchSize = 10)
    {
        PerformanceMonitor::startTimer('migration_batch');
        
        try {
            // Get pending migrations
            $migrations = self::getPendingMigrations();
            $batches = array_chunk($migrations, $batchSize);
            
            foreach ($batches as $batch) {
                DB::transaction(function() use ($batch) {
                    foreach ($batch as $migration) {
                        Artisan::call('migrate', [
                            '--path' => $migration,
                            '--force' => true
                        ]);
                    }
                });
                
                // Memory cleanup between batches
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }
            
        } finally {
            PerformanceMonitor::endTimer('migration_batch');
        }
    }

    public static function optimizeMemoryUsage()
    {
        // Increase memory limit for large operations
        $currentLimit = ini_get('memory_limit');
        $newLimit = self::calculateOptimalMemoryLimit();
        
        if ($newLimit > self::parseMemoryLimit($currentLimit)) {
            ini_set('memory_limit', $newLimit);
            LogManager::logOperation('memory_limit_increased', [
                'from' => $currentLimit,
                'to' => $newLimit
            ]);
        }

        // Set optimal execution time
        set_time_limit(300); // 5 minutes
        
        // Configure PDO for memory efficiency
        DB::getPdo()->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    }

    private static function getPendingMigrations()
    {
        // Simplified - in real implementation, scan migration files
        return glob(database_path('migrations/*.php'));
    }

    private static function calculateOptimalMemoryLimit()
    {
        $available = self::getAvailableMemory();
        $recommended = max(256 * 1024 * 1024, $available * 0.8); // 256MB or 80% of available
        
        return self::formatMemoryLimit($recommended);
    }

    private static function getAvailableMemory()
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage(true);
        }
        
        return 128 * 1024 * 1024; // Default 128MB
    }

    private static function parseMemoryLimit($limit)
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit)-1]);
        $limit = (int) $limit;
        
        switch($last) {
            case 'g': $limit *= 1024;
            case 'm': $limit *= 1024;
            case 'k': $limit *= 1024;
        }
        
        return $limit;
    }

    private static function formatMemoryLimit($bytes)
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024 * 1024)) . 'G';
        } elseif ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024)) . 'M';
        } else {
            return round($bytes / 1024) . 'K';
        }
    }
}