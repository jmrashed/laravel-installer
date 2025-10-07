<?php

namespace Jmrashed\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Helpers\DependencyChecker;
use Jmrashed\LaravelInstaller\Helpers\LogManager;

class DependencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip dependency check for dependency-related routes
        if ($request->is('installer/dependencies*')) {
            return $next($request);
        }

        try {
            $critical = DependencyChecker::checkCriticalDependencies();
            
            foreach ($critical as $dep) {
                if ($dep['status'] === 'incompatible' || $dep['status'] === 'missing') {
                    LogManager::logOperation('dependency_check_failed', [
                        'failed_dependency' => $dep['name'],
                        'status' => $dep['status']
                    ]);
                    
                    return redirect()->route('LaravelInstaller::dependencies')
                        ->with('error', "Critical dependency issue: {$dep['name']}");
                }
            }
            
        } catch (\Exception $e) {
            LogManager::logError('Dependency middleware failed', $e);
            // Continue anyway - don't block installation for dependency check failures
        }

        return $next($request);
    }
}