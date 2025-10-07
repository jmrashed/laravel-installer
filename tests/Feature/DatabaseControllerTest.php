<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class DatabaseControllerTest extends TestCase
{
    public function test_migrate_creates_backup_and_runs_migration()
    {
        $response = $this->postJson('/installer/database/migrate', [
            'seed' => false,
            'batch_size' => 10
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'backup_id',
            'performance'
        ]);
    }

    public function test_rollback_restores_database()
    {
        Cache::put('installer_backup_id', 'test_backup_123', 3600);
        
        $response = $this->postJson('/installer/database/rollback');
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_check_backup_returns_status()
    {
        Cache::put('installer_backup_id', 'test_backup_123', 3600);
        
        $response = $this->getJson('/installer/database/backup-status');
        
        $response->assertStatus(200);
        $response->assertJson([
            'has_backup' => true,
            'backup_id' => 'test_backup_123'
        ]);
    }
}