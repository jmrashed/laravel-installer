<?php

namespace Tests\Feature;

use Tests\TestCase;

class CacheQueueControllerTest extends TestCase
{
    public function test_clear_caches_succeeds()
    {
        $response = $this->postJson('/install/api/cache/clear');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'results'
        ]);
    }

    public function test_setup_queues_with_sync_driver()
    {
        $response = $this->postJson('/install/api/queue/setup', [
            'queue_driver' => 'sync'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_setup_queues_with_redis_driver()
    {
        $response = $this->postJson('/install/api/queue/setup', [
            'queue_driver' => 'redis',
            'redis_host' => '127.0.0.1',
            'redis_port' => 6379
        ]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_setup_scheduler_creates_config()
    {
        $response = $this->postJson('/install/api/scheduler/setup');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'result' => ['cron_entry', 'info_file']
        ]);
    }

    public function test_optimize_application()
    {
        $response = $this->postJson('/install/api/cache/optimize');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'results'
        ]);
    }

    public function test_cache_queue_page_loads()
    {
        $response = $this->visitInstallerStep('/install/cache-queue');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.cache-queue');
    }
}