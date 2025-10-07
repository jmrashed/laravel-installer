<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jmrashed\LaravelInstaller\Helpers\DependencyChecker;
use Jmrashed\LaravelInstaller\Helpers\LogManager;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;

class DependencyController extends Controller
{
    public function index()
    {
        try {
            $dependencies = DependencyChecker::checkComposerDependencies();
            $critical = DependencyChecker::checkCriticalDependencies();
            
            return view('vendor.installer.dependencies', compact('dependencies', 'critical'));
        } catch (Exception $e) {
            LogManager::logError('Dependency check failed', $e);
            return view('vendor.installer.dependencies', [
                'error' => $e->getMessage(),
                'dependencies' => null,
                'critical' => null
            ]);
        }
    }

    public function check()
    {
        try {
            $dependencies = DependencyChecker::checkComposerDependencies();
            $critical = DependencyChecker::checkCriticalDependencies();
            
            $hasErrors = $this->hasCompatibilityErrors($dependencies, $critical);
            
            LogManager::logOperation('dependency_check_completed', [
                'has_errors' => $hasErrors,
                'total_dependencies' => count($dependencies['dependencies']),
                'critical_count' => count($critical)
            ]);

            if (!$hasErrors) {
                ProgressTracker::setStep('dependencies', 'completed');
            }
            
            return response()->json([
                'success' => !$hasErrors,
                'dependencies' => $dependencies,
                'critical' => $critical,
                'can_proceed' => !$hasErrors
            ]);
            
        } catch (Exception $e) {
            LogManager::logError('Dependency check API failed', $e);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'can_proceed' => false
            ], 500);
        }
    }

    public function install(Request $request)
    {
        try {
            $packages = $request->input('packages', []);
            
            if (empty($packages)) {
                throw new Exception('No packages specified for installation');
            }

            LogManager::logOperation('dependency_install_started', ['packages' => $packages]);
            
            $results = [];
            foreach ($packages as $package) {
                $result = $this->installPackage($package);
                $results[$package] = $result;
            }
            
            LogManager::logOperation('dependency_install_completed', ['results' => $results]);
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Dependencies installed successfully'
            ]);
            
        } catch (Exception $e) {
            LogManager::logError('Dependency installation failed', $e);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function hasCompatibilityErrors($dependencies, $critical)
    {
        // Check critical dependencies
        foreach ($critical as $dep) {
            if ($dep['status'] === 'incompatible' || $dep['status'] === 'missing') {
                return true;
            }
        }

        // Check required dependencies
        foreach ($dependencies['dependencies'] as $dep) {
            if ($dep['status'] === 'incompatible' || $dep['status'] === 'missing') {
                return true;
            }
        }

        return false;
    }

    private function installPackage($package)
    {
        try {
            $command = "composer require {$package} --no-interaction 2>&1";
            $output = shell_exec($command);
            
            return [
                'success' => strpos($output, 'Package operations:') !== false,
                'output' => $output
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}