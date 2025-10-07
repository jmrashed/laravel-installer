<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Jmrashed\LaravelInstaller\Helpers\DatabaseBackupManager;

class DatabaseBackupManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure backup directory exists
        $backupDir = storage_path('installer/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
    }

    public function test_create_sqlite_backup()
    {
        // Create temporary SQLite database
        $dbPath = storage_path('test.sqlite');
        touch($dbPath);
        
        config(['database.connections.sqlite.database' => $dbPath]);
        
        try {
            $backupId = DatabaseBackupManager::createBackup('sqlite');
            $this->assertIsString($backupId);
            $this->assertStringStartsWith('backup_', $backupId);
        } finally {
            unlink($dbPath);
        }
    }

    public function test_create_backup_with_invalid_driver_throws_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Backup not supported for driver: invalid');
        
        config(['database.connections.invalid.driver' => 'invalid']);
        DatabaseBackupManager::createBackup('invalid');
    }

    public function test_restore_sqlite_backup()
    {
        // Create test backup file
        $backupId = 'test_backup_' . time();
        $backupPath = storage_path("installer/backups/{$backupId}.sql");
        $dbPath = storage_path('test_restore.sqlite');
        
        touch($backupPath);
        config(['database.connections.sqlite.database' => $dbPath]);
        
        try {
            $result = DatabaseBackupManager::restoreBackup($backupId);
            $this->assertTrue($result);
        } finally {
            if (file_exists($backupPath)) unlink($backupPath);
            if (file_exists($dbPath)) unlink($dbPath);
        }
    }

    public function test_clean_old_backups()
    {
        // Create old backup file
        $oldBackup = storage_path('installer/backups/old_backup.sql');
        touch($oldBackup);
        
        // Set file time to 8 days ago
        touch($oldBackup, time() - (8 * 24 * 60 * 60));
        
        $cleaned = DatabaseBackupManager::cleanOldBackups(7);
        
        $this->assertGreaterThanOrEqual(1, $cleaned);
        $this->assertFileDoesNotExist($oldBackup);
    }

    public function test_restore_nonexistent_backup_throws_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Backup file not found');
        
        DatabaseBackupManager::restoreBackup('nonexistent_backup');
    }
}