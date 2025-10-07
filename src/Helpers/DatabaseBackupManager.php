<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DatabaseBackupManager
{
    public static function createBackup($connection = null)
    {
        $connection = $connection ?: config('database.default');
        $config = config("database.connections.{$connection}");
        
        if (!$config) {
            throw new Exception("Database connection '{$connection}' not found");
        }

        $backupId = 'backup_' . time() . '_' . uniqid();
        $backupPath = storage_path("installer/backups/{$backupId}.sql");
        
        self::ensureBackupDirectory();
        
        try {
            switch ($config['driver']) {
                case 'mysql':
                    return self::createMysqlBackup($config, $backupPath, $backupId);
                case 'pgsql':
                    return self::createPostgresBackup($config, $backupPath, $backupId);
                case 'sqlite':
                    return self::createSqliteBackup($config, $backupPath, $backupId);
                default:
                    throw new Exception("Backup not supported for driver: {$config['driver']}");
            }
        } catch (Exception $e) {
            LogManager::logError('Database backup failed', $e);
            throw $e;
        }
    }

    public static function restoreBackup($backupId)
    {
        $backupPath = storage_path("installer/backups/{$backupId}.sql");
        
        if (!file_exists($backupPath)) {
            throw new Exception("Backup file not found: {$backupId}");
        }

        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        try {
            switch ($config['driver']) {
                case 'mysql':
                    return self::restoreMysqlBackup($config, $backupPath);
                case 'pgsql':
                    return self::restorePostgresBackup($config, $backupPath);
                case 'sqlite':
                    return self::restoreSqliteBackup($config, $backupPath);
                default:
                    throw new Exception("Restore not supported for driver: {$config['driver']}");
            }
        } catch (Exception $e) {
            LogManager::logError('Database restore failed', $e, ['backup_id' => $backupId]);
            throw $e;
        }
    }

    private static function createMysqlBackup($config, $backupPath, $backupId)
    {
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 3306),
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception('MySQL backup failed');
        }

        return $backupId;
    }

    private static function createSqliteBackup($config, $backupPath, $backupId)
    {
        $dbPath = $config['database'];
        
        if (!file_exists($dbPath)) {
            throw new Exception('SQLite database file not found');
        }

        if (!copy($dbPath, $backupPath)) {
            throw new Exception('Failed to copy SQLite database');
        }

        return $backupId;
    }

    private static function restoreMysqlBackup($config, $backupPath)
    {
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 3306),
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception('MySQL restore failed');
        }

        return true;
    }

    private static function restoreSqliteBackup($config, $backupPath)
    {
        $dbPath = $config['database'];
        
        if (!copy($backupPath, $dbPath)) {
            throw new Exception('Failed to restore SQLite database');
        }

        return true;
    }

    private static function ensureBackupDirectory()
    {
        $backupDir = storage_path('installer/backups');
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
    }

    public static function cleanOldBackups($days = 7)
    {
        $backupDir = storage_path('installer/backups');
        $cutoff = time() - ($days * 24 * 60 * 60);
        $cleaned = 0;

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff) {
                    unlink($file);
                    $cleaned++;
                }
            }
        }

        return $cleaned;
    }
}