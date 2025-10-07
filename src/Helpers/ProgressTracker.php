<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ProgressTracker
{
    const STEPS = [
        'welcome' => ['name' => 'Welcome', 'weight' => 5],
        'dependencies' => ['name' => 'Dependency Check', 'weight' => 8],
        'requirements' => ['name' => 'Requirements Check', 'weight' => 8],
        'permissions' => ['name' => 'Permissions Check', 'weight' => 8],
        'environment' => ['name' => 'Environment Setup', 'weight' => 18],
        'database' => ['name' => 'Database Configuration', 'weight' => 20],
        'migration' => ['name' => 'Database Migration', 'weight' => 15],
        'optimization' => ['name' => 'Cache & Queue Setup', 'weight' => 13],
        'finished' => ['name' => 'Installation Complete', 'weight' => 5]
    ];

    public static function setStep($step, $status = 'completed', $data = [])
    {
        $sessionId = Session::getId();
        $progress = self::getProgress();
        
        $progress['steps'][$step] = [
            'status' => $status,
            'completed_at' => now()->toISOString(),
            'data' => $data
        ];
        
        $progress['current_step'] = $step;
        $progress['percentage'] = self::calculatePercentage($progress['steps']);
        
        Cache::put("installer_progress_{$sessionId}", $progress, 3600);
        
        LogManager::logOperation('progress_updated', [
            'step' => $step,
            'status' => $status,
            'percentage' => $progress['percentage']
        ]);
        
        return $progress;
    }

    public static function getProgress()
    {
        $sessionId = Session::getId();
        return Cache::get("installer_progress_{$sessionId}", [
            'steps' => [],
            'current_step' => 'welcome',
            'percentage' => 0,
            'started_at' => now()->toISOString()
        ]);
    }

    public static function canResume($step)
    {
        $progress = self::getProgress();
        $stepKeys = array_keys(self::STEPS);
        $currentIndex = array_search($progress['current_step'], $stepKeys);
        $requestedIndex = array_search($step, $stepKeys);
        
        return $requestedIndex <= $currentIndex + 1;
    }

    public static function getNextStep($currentStep)
    {
        $stepKeys = array_keys(self::STEPS);
        $currentIndex = array_search($currentStep, $stepKeys);
        
        return $stepKeys[$currentIndex + 1] ?? null;
    }

    private static function calculatePercentage($completedSteps)
    {
        $totalWeight = array_sum(array_column(self::STEPS, 'weight'));
        $completedWeight = 0;
        
        foreach ($completedSteps as $step => $data) {
            if ($data['status'] === 'completed' && isset(self::STEPS[$step])) {
                $completedWeight += self::STEPS[$step]['weight'];
            }
        }
        
        return round(($completedWeight / $totalWeight) * 100);
    }

    public static function reset()
    {
        $sessionId = Session::getId();
        Cache::forget("installer_progress_{$sessionId}");
    }
}