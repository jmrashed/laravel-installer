<?php

namespace Jmrashed\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;

class ProgressMiddleware
{
    private $stepRouteMap = [
        'LaravelInstaller::welcome' => 'welcome',
        'LaravelInstaller::requirements' => 'requirements',
        'LaravelInstaller::permissions' => 'permissions',
        'LaravelInstaller::environmentWizard' => 'environment',
        'LaravelInstaller::database' => 'database',
        'LaravelInstaller::final' => 'migration',
        'LaravelInstaller::finished' => 'finished'
    ];

    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        $step = $this->stepRouteMap[$routeName] ?? null;
        
        if ($step) {
            // Check if user can access this step
            if (!ProgressTracker::canResume($step)) {
                $progress = ProgressTracker::getProgress();
                $currentStep = $progress['current_step'];
                
                // Redirect to current step
                return redirect()->route($this->getRouteForStep($currentStep));
            }
            
            // Update current step if moving forward
            $progress = ProgressTracker::getProgress();
            if ($this->isForwardStep($step, $progress['current_step'])) {
                ProgressTracker::setStep($step, 'in_progress');
            }
        }
        
        return $next($request);
    }

    private function getRouteForStep($step)
    {
        $routeMap = array_flip($this->stepRouteMap);
        return $routeMap[$step] ?? 'LaravelInstaller::welcome';
    }

    private function isForwardStep($newStep, $currentStep)
    {
        $steps = array_keys(ProgressTracker::STEPS);
        $newIndex = array_search($newStep, $steps);
        $currentIndex = array_search($currentStep, $steps);
        
        return $newIndex > $currentIndex;
    }
}