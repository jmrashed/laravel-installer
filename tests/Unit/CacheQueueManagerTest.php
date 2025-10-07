<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Jmrashed\LaravelInstaller\Helpers\CacheQueueManager;

class CacheQueueManagerTest extends TestCase
{
    public function test_clear_all_caches_returns_results()
    {
        $results = CacheQueueManager::clearAllCaches();
        
        $this->assertIsArray($results);
        $this->assertArrayHasKey('config:clear', $results);
        $this->assertArrayHasKey('route:clear', $results);
        $this->assertArrayHasKey('view:clear', $results);
        $this->assertArrayHasKey('cache:clear', $results);
        
        foreach ($results as $command => $result) {
            $this->assertArrayHasKey('success', $result);
            if (isset($result['description'])) {
                $this->assertIsString($result['description']);
            }
        }
    }

    public function test_setup_queues_with_sync_driver()
    {
        $config = ['queue_driver' => 'sync'];
        $result = CacheQueueManager::setupQueues($config);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('sync', $result['driver']);
    }

    public function test_setup_queues_with_redis_driver()
    {
        $config = [
            'queue_driver' => 'redis',
            'redis_host' => '127.0.0.1',
            'redis_password' => 'secret',
            'redis_port' => '6379'
        ];
        
        $result = CacheQueueManager::setupQueues($config);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('redis', $result['driver']);
    }

    public function test_setup_scheduler_creates_info_file()
    {
        $result = CacheQueueManager::setupScheduler();
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('cron_entry', $result);
        $this->assertArrayHasKey('info_file', $result);
        $this->assertStringContainsString('schedule:run', $result['cron_entry']);
    }

    public function test_optimize_application_caches_config()
    {
        $results = CacheQueueManager::optimizeApplication();
        
        $this->assertIsArray($results);
        $this->assertArrayHasKey('config:cache', $results);
        $this->assertArrayHasKey('route:cache', $results);
        $this->assertArrayHasKey('view:cache', $results);
        
        foreach ($results as $command => $result) {
            $this->assertArrayHasKey('success', $result);
        }
    }
}