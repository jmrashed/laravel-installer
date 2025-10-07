<?php

namespace Jmrashed\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Helpers\DependencyChecker;

class DependencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip dependency check for API routes and certain installer routes
        $skipRoutes = [
            'LaravelInstaller::welcome',
            'LaravelInstaller::api.dependencies.check',
            'LaravelInstaller::api.dependencies.install'
        ];

        $routeName = $request->route()->getName();
        
        if (in_array($routeName, $skipRoutes)) {
            return $next($request);
        }

        try {
            $dependencies = DependencyChecker::checkCriticalDependencies();
            
            foreach ($dependencies as $dependency) {
                if ($dependency['status'] === 'missing' || $dependency['status'] === 'incompatible') {
                    return redirect()->route('LaravelInstaller::dependencies')
                        ->with('error', 'Critical dependencies are missing or incompatible.');
                }
            }
        } catch (\Exception $e) {
            // Log error but don't block installation
            \Log::warning('Dependency check failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}