<?php

namespace Jmrashed\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Helpers\PerformanceMonitor;

class PerformanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $operation = $request->route()->getName() ?? 'unknown';
        
        PerformanceMonitor::startTimer($operation);
        PerformanceMonitor::snapshot('request_start');
        
        $response = $next($request);
        
        $metrics = PerformanceMonitor::endTimer($operation);
        
        // Add performance headers
        if ($metrics) {
            $response->headers->set('X-Execution-Time', round($metrics['execution_time'], 4));
            $response->headers->set('X-Memory-Usage', $this->formatBytes($metrics['memory_used']));
            $response->headers->set('X-Peak-Memory', $this->formatBytes($metrics['peak_memory']));
        }
        
        return $response;
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . 'MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . 'KB';
        }
        return $bytes . 'B';
    }
}