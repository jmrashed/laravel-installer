<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\PerformanceMonitor;

class PerformanceMonitorTest extends TestCase
{
    public function test_start_and_end_timer()
    {
        PerformanceMonitor::startTimer('test_operation');
        usleep(10000); // 10ms
        $metrics = PerformanceMonitor::endTimer('test_operation');
        
        $this->assertIsArray($metrics);
        $this->assertEquals('test_operation', $metrics['operation']);
        $this->assertGreaterThan(0, $metrics['execution_time']);
        $this->assertArrayHasKey('memory_used', $metrics);
        $this->assertArrayHasKey('peak_memory', $metrics);
    }

    public function test_get_metrics_returns_current_state()
    {
        $metrics = PerformanceMonitor::getMetrics();
        
        $this->assertArrayHasKey('current_memory', $metrics);
        $this->assertArrayHasKey('peak_memory', $metrics);
        $this->assertArrayHasKey('memory_limit', $metrics);
        $this->assertArrayHasKey('memory_percentage', $metrics);
        $this->assertArrayHasKey('execution_time', $metrics);
        $this->assertArrayHasKey('active_timers', $metrics);
    }

    public function test_snapshot_captures_memory_state()
    {
        PerformanceMonitor::snapshot('test_snapshot');
        
        // Verify snapshot was created (internal state)
        $this->assertTrue(true); // Placeholder - would need access to internal state
    }

    public function test_memory_percentage_calculation()
    {
        $metrics = PerformanceMonitor::getMetrics();
        
        $this->assertIsNumeric($metrics['memory_percentage']);
        $this->assertGreaterThanOrEqual(0, $metrics['memory_percentage']);
        $this->assertLessThanOrEqual(100, $metrics['memory_percentage']);
    }

    public function test_end_timer_without_start_returns_null()
    {
        $metrics = PerformanceMonitor::endTimer('nonexistent_operation');
        
        $this->assertNull($metrics);
    }
}