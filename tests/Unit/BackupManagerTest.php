<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Jmrashed\LaravelInstaller\Helpers\BackupManager;

class BackupManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (glob(base_path('.env.backup.*')) as $backup) {
            unlink($backup);
        }

        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            unlink($envPath);
        }

        $dbBackupDir = storage_path('installer');
        if (is_dir($dbBackupDir)) {
            foreach (glob($dbBackupDir . '/db_*_backup_*.json') as $backup) {
                unlink($backup);
            }
        }

        parent::tearDown();
    }

    public function test_create_env_backup_returns_null_when_no_env_file()
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            unlink($envPath);
        }

        $this->assertNull(BackupManager::createEnvBackup());
    }

    public function test_create_env_backup_copies_env_file()
    {
        file_put_contents(base_path('.env'), 'APP_NAME=Test');

        $backupPath = BackupManager::createEnvBackup();

        try {
            $this->assertIsString($backupPath);
            $this->assertFileExists($backupPath);
            $this->assertStringEqualsFile($backupPath, 'APP_NAME=Test');
        } finally {
            if ($backupPath && file_exists($backupPath)) {
                unlink($backupPath);
            }
        }
    }

    public function test_restore_env_backup_restores_content()
    {
        $backupPath = base_path('.env.backup.test');
        file_put_contents($backupPath, 'APP_NAME=Restored');

        try {
            $result = BackupManager::restoreEnvBackup($backupPath);

            $this->assertTrue($result);
            $this->assertStringEqualsFile(base_path('.env'), 'APP_NAME=Restored');
        } finally {
            if (file_exists($backupPath)) {
                unlink($backupPath);
            }
        }
    }

    public function test_restore_env_backup_returns_false_when_backup_missing()
    {
        Log::spy();

        $result = BackupManager::restoreEnvBackup(base_path('.env.backup.nonexistent'));

        $this->assertFalse($result);
        Log::shouldHaveReceived('error')->once();
    }

    public function test_create_database_schema_backup_returns_null_without_database()
    {
        config(['database.connections.testing.database' => null]);

        $this->assertNull(BackupManager::createDatabaseSchemaBackup('testing'));
    }

    public function test_create_database_schema_backup_logs_error_for_unsupported_driver()
    {
        Log::spy();

        // sqlite ":memory:" connection doesn't support "SHOW TABLES", so this should
        // gracefully fail and log an error rather than throw.
        $result = BackupManager::createDatabaseSchemaBackup('testing');

        $this->assertNull($result);
        Log::shouldHaveReceived('error')->once();
    }

    public function test_clean_old_backups_removes_stale_env_backups()
    {
        $oldBackup = base_path('.env.backup.old');
        file_put_contents($oldBackup, 'old');
        touch($oldBackup, time() - (8 * 24 * 60 * 60));

        $recentBackup = base_path('.env.backup.recent');
        file_put_contents($recentBackup, 'recent');

        try {
            $cleaned = BackupManager::cleanOldBackups(7);

            $this->assertGreaterThanOrEqual(1, $cleaned);
            $this->assertFileDoesNotExist($oldBackup);
            $this->assertFileExists($recentBackup);
        } finally {
            if (file_exists($recentBackup)) {
                unlink($recentBackup);
            }
            if (file_exists($oldBackup)) {
                unlink($oldBackup);
            }
        }
    }
}
