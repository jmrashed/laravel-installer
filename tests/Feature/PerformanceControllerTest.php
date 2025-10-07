<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class PerformanceControllerTest extends TestCase
{
    public function test_get_metrics_returns_performance_data()
    {
        $response = $this->getJson('/installer/performance/metrics');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_memory',
            'peak_memory',
            'memory_limit',
            'memory_percentage',
            'execution_time',
            'active_timers'
        ]);
    }

    public function test_get_history_returns_metrics_data()
    {
        Cache::put('installer_metrics_' . date('Y-m-d-H'), [
            ['operation' => 'test', 'execution_time' => 0.1, 'memory_used' => 1024]
        ], 3600);
        
        $response = $this->getJson('/installer/performance/history?hours=1');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'metrics',
            'summary'
        ]);
    }

    public function test_optimize_performance()
    {
        $response = $this->postJson('/installer/performance/optimize');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'metrics'
        ]);
    }
}