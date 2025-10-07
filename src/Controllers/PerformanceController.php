<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Jmrashed\LaravelInstaller\Helpers\PerformanceMonitor;

class PerformanceController extends Controller
{
    public function getMetrics()
    {
        return response()->json(PerformanceMonitor::getMetrics());
    }

    public function getHistory(Request $request)
    {
        $hours = $request->input('hours', 1);
        $metrics = [];
        
        for ($i = 0; $i < $hours; $i++) {
            $key = 'installer_metrics_' . date('Y-m-d-H', strtotime("-{$i} hours"));
            $hourMetrics = Cache::get($key, []);
            $metrics = array_merge($metrics, $hourMetrics);
        }
        
        return response()->json([
            'metrics' => array_slice($metrics, -50), // Last 50 entries
            'summary' => $this->calculateSummary($metrics)
        ]);
    }

    public function optimize()
    {
        PerformanceMonitor::startTimer('optimization');
        
        try {
            // Clear various caches
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            $metrics = PerformanceMonitor::endTimer('optimization');
            
            return response()->json([
                'success' => true,
                'message' => 'Performance optimization completed',
                'metrics' => $metrics
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Optimization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateSummary($metrics)
    {
        if (empty($metrics)) {
            return null;
        }
        
        $executionTimes = array_column($metrics, 'execution_time');
        $memoryUsages = array_column($metrics, 'memory_used');
        
        return [
            'avg_execution_time' => round(array_sum($executionTimes) / count($executionTimes), 4),
            'max_execution_time' => max($executionTimes),
            'avg_memory_usage' => round(array_sum($memoryUsages) / count($memoryUsages)),
            'max_memory_usage' => max($memoryUsages),
            'total_operations' => count($metrics)
        ];
    }
}