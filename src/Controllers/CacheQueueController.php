<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jmrashed\LaravelInstaller\Helpers\CacheQueueManager;
use Jmrashed\LaravelInstaller\Helpers\LogManager;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;

class CacheQueueController extends Controller
{
    public function index()
    {
        return view('vendor.installer.cache-queue');
    }

    public function clearCaches()
    {
        try {
            LogManager::logOperation('cache_clear_started');
            
            $results = CacheQueueManager::clearAllCaches();
            
            LogManager::logOperation('cache_clear_completed', ['results' => $results]);
            
            return response()->json([
                'success' => true,
                'message' => 'Caches cleared successfully',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            LogManager::logError('Cache clear failed', $e);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear caches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function setupQueues(Request $request)
    {
        try {
            $config = $request->validate([
                'queue_driver' => 'required|in:sync,database,redis',
                'redis_host' => 'nullable|string',
                'redis_password' => 'nullable|string',
                'redis_port' => 'nullable|integer'
            ]);

            LogManager::logOperation('queue_setup_started', ['driver' => $config['queue_driver']]);
            
            $result = CacheQueueManager::setupQueues($config);
            
            LogManager::logOperation('queue_setup_completed', $result);
            
            return response()->json([
                'success' => true,
                'message' => 'Queue configuration updated',
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            LogManager::logError('Queue setup failed', $e);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to setup queues',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function setupScheduler()
    {
        try {
            LogManager::logOperation('scheduler_setup_started');
            
            $result = CacheQueueManager::setupScheduler();
            
            LogManager::logOperation('scheduler_setup_completed', $result);
            
            return response()->json([
                'success' => true,
                'message' => 'Scheduler configuration created',
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            LogManager::logError('Scheduler setup failed', $e);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to setup scheduler',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function optimize()
    {
        try {
            LogManager::logOperation('optimization_started');
            
            $results = CacheQueueManager::optimizeApplication();
            
            LogManager::logOperation('optimization_completed', ['results' => $results]);
            ProgressTracker::setStep('optimization', 'completed');
            
            return response()->json([
                'success' => true,
                'message' => 'Application optimized successfully',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            LogManager::logError('Optimization failed', $e);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize application',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}