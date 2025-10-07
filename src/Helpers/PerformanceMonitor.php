<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\Cache;

class PerformanceMonitor
{
    private static $timers = [];
    private static $memorySnapshots = [];

    public static function startTimer($operation)
    {
        self::$timers[$operation] = [
            'start' => microtime(true),
            'memory_start' => memory_get_usage(true),
            'peak_memory_start' => memory_get_peak_usage(true)
        ];
    }

    public static function endTimer($operation)
    {
        if (!isset(self::$timers[$operation])) {
            return null;
        }

        $timer = self::$timers[$operation];
        $metrics = [
            'operation' => $operation,
            'execution_time' => microtime(true) - $timer['start'],
            'memory_used' => memory_get_usage(true) - $timer['memory_start'],
            'peak_memory' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'timestamp' => now()->toISOString()
        ];

        self::logMetrics($metrics);
        unset(self::$timers[$operation]);
        
        return $metrics;
    }

    public static function snapshot($label = 'default')
    {
        self::$memorySnapshots[$label] = [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'timestamp' => microtime(true)
        ];
    }

    public static function getMetrics()
    {
        return [
            'current_memory' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'memory_limit' => self::parseMemoryLimit(ini_get('memory_limit')),
            'memory_percentage' => self::getMemoryPercentage(),
            'execution_time' => self::getExecutionTime(),
            'active_timers' => count(self::$timers)
        ];
    }

    private static function logMetrics($metrics)
    {
        LogManager::logOperation('performance_metrics', $metrics, 'info');
        
        // Store in cache for dashboard
        $key = 'installer_metrics_' . date('Y-m-d-H');
        $existing = Cache::get($key, []);
        $existing[] = $metrics;
        Cache::put($key, array_slice($existing, -100), 3600); // Keep last 100 entries
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

    private static function getMemoryPercentage()
    {
        $current = memory_get_usage(true);
        $limit = self::parseMemoryLimit(ini_get('memory_limit'));
        
        return $limit > 0 ? round(($current / $limit) * 100, 2) : 0;
    }

    private static function getExecutionTime()
    {
        return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    }
}