<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Jmrashed\LaravelInstaller\Helpers\DatabaseBackupManager;
use Jmrashed\LaravelInstaller\Helpers\LogManager;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;
use Jmrashed\LaravelInstaller\Helpers\PerformanceMonitor;
use Jmrashed\LaravelInstaller\Helpers\DatabaseOptimizer;

class DatabaseController extends Controller
{
    public function migrate(Request $request)
    {
        $backupId = null;
        PerformanceMonitor::startTimer('database_migration');
        
        try {
            // Optimize for large databases
            DatabaseOptimizer::optimizeMemoryUsage();
            DatabaseOptimizer::optimizeForLargeDatabase();
            
            // Create backup before migration
            PerformanceMonitor::startTimer('backup_creation');
            LogManager::logOperation('migration_backup_started');
            $backupId = DatabaseBackupManager::createBackup();
            Cache::put('installer_backup_id', $backupId, 3600);
            PerformanceMonitor::endTimer('backup_creation');
            
            LogManager::logOperation('migration_started', ['backup_id' => $backupId]);
            ProgressTracker::setStep('migration', 'in_progress');
            
            // Run migrations with performance monitoring
            PerformanceMonitor::startTimer('migration_execution');
            if ($request->input('batch_size')) {
                DatabaseOptimizer::runMigrationsInBatches($request->input('batch_size', 10));
            } else {
                Artisan::call('migrate', ['--force' => true]);
            }
            PerformanceMonitor::endTimer('migration_execution');
            
            // Run seeders if requested
            if ($request->input('seed', false)) {
                PerformanceMonitor::startTimer('seeding');
                Artisan::call('db:seed', ['--force' => true]);
                PerformanceMonitor::endTimer('seeding');
            }
            
            $migrationMetrics = PerformanceMonitor::endTimer('database_migration');
            LogManager::logOperation('migration_completed', [
                'backup_id' => $backupId,
                'performance' => $migrationMetrics
            ]);
            ProgressTracker::setStep('migration', 'completed');
            
            return response()->json([
                'success' => true,
                'message' => 'Database migration completed successfully',
                'backup_id' => $backupId,
                'performance' => $migrationMetrics
            ]);
            
        } catch (Exception $e) {
            LogManager::logError('Migration failed', $e, ['backup_id' => $backupId]);
            
            // Attempt rollback if backup exists
            if ($backupId) {
                try {
                    $this->rollback($backupId);
                    return response()->json([
                        'success' => false,
                        'message' => 'Migration failed. Database restored from backup.',
                        'error' => $e->getMessage()
                    ], 500);
                } catch (Exception $rollbackException) {
                    LogManager::logError('Rollback failed', $rollbackException, ['backup_id' => $backupId]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Migration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rollback($backupId = null)
    {
        try {
            $backupId = $backupId ?: Cache::get('installer_backup_id');
            
            if (!$backupId) {
                throw new Exception('No backup ID found');
            }
            
            LogManager::logOperation('rollback_started', ['backup_id' => $backupId]);
            
            DatabaseBackupManager::restoreBackup($backupId);
            
            LogManager::logOperation('rollback_completed', ['backup_id' => $backupId]);
            
            return response()->json([
                'success' => true,
                'message' => 'Database restored successfully'
            ]);
            
        } catch (Exception $e) {
            LogManager::logError('Rollback failed', $e, ['backup_id' => $backupId]);
            
            return response()->json([
                'success' => false,
                'message' => 'Rollback failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkBackup()
    {
        $backupId = Cache::get('installer_backup_id');
        
        return response()->json([
            'has_backup' => !is_null($backupId),
            'backup_id' => $backupId
        ]);
    }
}