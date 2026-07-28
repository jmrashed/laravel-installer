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
        if (!preg_match('/^backup_\d+_[a-f0-9]+$/', (string) $backupId)) {
            throw new Exception('Invalid backup identifier');
        }

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
            'mysqldump --host=%s --port=%s --user=%s %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 3306),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        $returnCode = self::runWithMysqlPassword($config['password'] ?? '', $command);

        if ($returnCode !== 0) {
            throw new Exception('MySQL backup failed');
        }

        self::lockDownBackupFile($backupPath);

        return $backupId;
    }

    private static function createPostgresBackup($config, $backupPath, $backupId)
    {
        $command = sprintf(
            'pg_dump --host=%s --port=%s --username=%s --no-password %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 5432),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        $returnCode = self::runWithPostgresPassword($config['password'] ?? '', $command);

        if ($returnCode !== 0) {
            throw new Exception('PostgreSQL backup failed');
        }

        self::lockDownBackupFile($backupPath);

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

        self::lockDownBackupFile($backupPath);

        return $backupId;
    }

    private static function restoreMysqlBackup($config, $backupPath)
    {
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s %s < %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 3306),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        $returnCode = self::runWithMysqlPassword($config['password'] ?? '', $command);

        if ($returnCode !== 0) {
            throw new Exception('MySQL restore failed');
        }

        return true;
    }

    private static function restorePostgresBackup($config, $backupPath)
    {
        $command = sprintf(
            'psql --host=%s --port=%s --username=%s --no-password %s < %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 5432),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        $returnCode = self::runWithPostgresPassword($config['password'] ?? '', $command);

        if ($returnCode !== 0) {
            throw new Exception('PostgreSQL restore failed');
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

    /**
     * Run a mysql/mysqldump command with the password passed via MYSQL_PWD
     * instead of a --password= CLI flag, so it isn't visible in `ps aux`.
     */
    private static function runWithMysqlPassword($password, $command)
    {
        $previous = getenv('MYSQL_PWD');
        putenv('MYSQL_PWD=' . $password);

        try {
            exec($command, $output, $returnCode);
        } finally {
            putenv($previous === false ? 'MYSQL_PWD' : "MYSQL_PWD={$previous}");
        }

        return $returnCode;
    }

    /**
     * Run a psql/pg_dump command with the password passed via PGPASSWORD
     * instead of on the command line.
     */
    private static function runWithPostgresPassword($password, $command)
    {
        $previous = getenv('PGPASSWORD');
        putenv('PGPASSWORD=' . $password);

        try {
            exec($command, $output, $returnCode);
        } finally {
            putenv($previous === false ? 'PGPASSWORD' : "PGPASSWORD={$previous}");
        }

        return $returnCode;
    }

    private static function lockDownBackupFile($backupPath)
    {
        if (file_exists($backupPath)) {
            chmod($backupPath, 0600);
        }
    }

    private static function ensureBackupDirectory()
    {
        $backupDir = storage_path('installer/backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0700, true);
        } else {
            chmod($backupDir, 0700);
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