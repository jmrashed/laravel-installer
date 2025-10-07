<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupManager
{
    /**
     * Create environment file backup
     */
    public static function createEnvBackup()
    {
        try {
            $envPath = base_path('.env');
            if (!file_exists($envPath)) {
                return null;
            }

            $backupName = '.env.backup.' . date('Y-m-d_H-i-s');
            $backupPath = base_path($backupName);
            
            if (copy($envPath, $backupPath)) {
                Log::info('Environment backup created', ['path' => $backupPath]);
                return $backupPath;
            }
        } catch (Exception $e) {
            Log::error('Failed to create environment backup: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Restore environment from backup
     */
    public static function restoreEnvBackup($backupPath)
    {
        try {
            if (!file_exists($backupPath)) {
                throw new Exception('Backup file not found');
            }

            $envPath = base_path('.env');
            if (copy($backupPath, $envPath)) {
                Log::info('Environment restored from backup', ['backup' => $backupPath]);
                return true;
            }
        } catch (Exception $e) {
            Log::error('Failed to restore environment backup: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Create database structure backup (schema only)
     */
    public static function createDatabaseSchemaBackup($connection = null)
    {
        try {
            $connection = $connection ?: config('database.default');
            $database = config("database.connections.{$connection}.database");
            
            if (!$database) {
                return null;
            }

            $tables = DB::connection($connection)->select('SHOW TABLES');
            $backup = [
                'timestamp' => now()->toISOString(),
                'database' => $database,
                'tables' => []
            ];

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                $createTable = DB::connection($connection)->select("SHOW CREATE TABLE `{$tableName}`");
                $backup['tables'][$tableName] = $createTable[0]->{'Create Table'};
            }

            $backupPath = storage_path('installer/db_schema_backup_' . time() . '.json');
            
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }

            file_put_contents($backupPath, json_encode($backup, JSON_PRETTY_PRINT));
            
            Log::info('Database schema backup created', [
                'database' => $database,
                'path' => $backupPath,
                'tables_count' => count($backup['tables'])
            ]);

            return $backupPath;
        } catch (Exception $e) {
            Log::error('Failed to create database schema backup: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clean old backup files
     */
    public static function cleanOldBackups($days = 7)
    {
        try {
            $cutoff = time() - ($days * 24 * 60 * 60);
            $cleaned = 0;

            // Clean env backups
            $envBackups = glob(base_path('.env.backup.*'));
            foreach ($envBackups as $backup) {
                if (filemtime($backup) < $cutoff) {
                    unlink($backup);
                    $cleaned++;
                }
            }

            // Clean database backups
            $dbBackupDir = storage_path('installer');
            if (is_dir($dbBackupDir)) {
                $dbBackups = glob($dbBackupDir . '/db_*_backup_*.json');
                foreach ($dbBackups as $backup) {
                    if (filemtime($backup) < $cutoff) {
                        unlink($backup);
                        $cleaned++;
                    }
                }
            }

            Log::info("Cleaned {$cleaned} old backup files");
            return $cleaned;
        } catch (Exception $e) {
            Log::error('Failed to clean old backups: ' . $e->getMessage());
            return 0;
        }
    }
}