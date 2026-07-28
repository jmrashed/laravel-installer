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
        $currentBytes = self::parseMemoryLimit($currentLimit);
        $newLimit = self::calculateOptimalMemoryLimit($currentBytes);

        if ($newLimit !== null && $newLimit > $currentBytes) {
            ini_set('memory_limit', self::formatMemoryLimit($newLimit));
            LogManager::logOperation('memory_limit_increased', [
                'from' => $currentLimit,
                'to' => self::formatMemoryLimit($newLimit)
            ]);
        }

        // set_time_limit() has no effect under the CLI SAPI (max_execution_time
        // defaults to 0/unlimited there) and can be overridden by pool config
        // under PHP-FPM; only worth attempting outside CLI.
        if (PHP_SAPI !== 'cli') {
            set_time_limit(300); // 5 minutes
        }

        // MYSQL_ATTR_USE_BUFFERED_QUERY is a MySQL-specific PDO attribute;
        // setting it unconditionally throws/warns on other drivers (Postgres,
        // SQLite), both of which this package advertises support for.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::getPdo()->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        }
    }

    private static function getPendingMigrations()
    {
        // Simplified - in real implementation, scan migration files
        return glob(database_path('migrations/*.php'));
    }

    /**
     * Recommend a higher memory_limit based on the *configured* limit, not
     * the current process's own memory_get_usage() (which only reflects
     * what this process has already allocated, not what's actually
     * available on the system, and would make the "80% of available"
     * calculation meaningless).
     *
     * @return int|null Byte count to raise the limit to, or null if the
     *                   current limit is already unlimited (-1).
     */
    private static function calculateOptimalMemoryLimit($currentBytes)
    {
        if ($currentBytes < 0) {
            return null; // already unlimited
        }

        return max(256 * 1024 * 1024, $currentBytes * 2);
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