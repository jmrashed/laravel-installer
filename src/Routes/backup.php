<?php

use Illuminate\Support\Facades\Route;
use Jmrashed\LaravelInstaller\Controllers\DatabaseController;
use Jmrashed\LaravelInstaller\Controllers\ProgressController;
use Jmrashed\LaravelInstaller\Controllers\PerformanceController;
use Jmrashed\LaravelInstaller\Controllers\DependencyController;
use Jmrashed\LaravelInstaller\Controllers\CacheQueueController;

Route::group(['prefix' => 'installer', 'middleware' => ['web']], function () {
    Route::post('database/migrate', [DatabaseController::class, 'migrate'])->name('LaravelInstaller::database.migrate');
    Route::post('database/rollback', [DatabaseController::class, 'rollback'])->name('LaravelInstaller::database.rollback');
    Route::get('database/backup-status', [DatabaseController::class, 'checkBackup'])->name('LaravelInstaller::database.backup-status');
    
    Route::get('progress', [ProgressController::class, 'getProgress'])->name('LaravelInstaller::progress');
    Route::post('progress/update', [ProgressController::class, 'updateProgress'])->name('LaravelInstaller::progress.update');
    Route::post('progress/reset', [ProgressController::class, 'reset'])->name('LaravelInstaller::progress.reset');
    
    Route::get('performance/metrics', [PerformanceController::class, 'getMetrics'])->name('LaravelInstaller::performance.metrics');
    Route::get('performance/history', [PerformanceController::class, 'getHistory'])->name('LaravelInstaller::performance.history');
    Route::post('performance/optimize', [PerformanceController::class, 'optimize'])->name('LaravelInstaller::performance.optimize');
    
    Route::get('dependencies', [DependencyController::class, 'index'])->name('LaravelInstaller::dependencies');
    Route::get('dependencies/check', [DependencyController::class, 'check'])->name('LaravelInstaller::dependencies.check');
    Route::post('dependencies/install', [DependencyController::class, 'install'])->name('LaravelInstaller::dependencies.install');
    
    Route::get('cache-queue', [CacheQueueController::class, 'index'])->name('LaravelInstaller::cache-queue');
    Route::post('cache/clear', [CacheQueueController::class, 'clearCaches'])->name('LaravelInstaller::cache.clear');
    Route::post('cache/optimize', [CacheQueueController::class, 'optimize'])->name('LaravelInstaller::cache.optimize');
    Route::post('queue/setup', [CacheQueueController::class, 'setupQueues'])->name('LaravelInstaller::queue.setup');
    Route::post('scheduler/setup', [CacheQueueController::class, 'setupScheduler'])->name('LaravelInstaller::scheduler.setup');
});