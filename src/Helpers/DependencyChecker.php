<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Exception;

class DependencyChecker
{
    public static function checkComposerDependencies()
    {
        $composerPath = base_path('composer.json');
        $lockPath = base_path('composer.lock');
        
        if (!file_exists($composerPath)) {
            throw new Exception('composer.json not found');
        }

        $composer = json_decode(file_get_contents($composerPath), true);
        $installed = self::getInstalledPackages();
        
        $results = [
            'composer_version' => self::getComposerVersion(),
            'php_version' => PHP_VERSION,
            'dependencies' => [],
            'dev_dependencies' => [],
            'conflicts' => [],
            'missing' => [],
            'outdated' => []
        ];

        // Check required dependencies
        if (isset($composer['require'])) {
            foreach ($composer['require'] as $package => $version) {
                $results['dependencies'][] = self::checkPackage($package, $version, $installed);
            }
        }

        // Check dev dependencies
        if (isset($composer['require-dev'])) {
            foreach ($composer['require-dev'] as $package => $version) {
                $results['dev_dependencies'][] = self::checkPackage($package, $version, $installed, true);
            }
        }

        return $results;
    }

    private static function checkPackage($package, $requiredVersion, $installed, $isDev = false)
    {
        $status = 'missing';
        $installedVersion = null;
        $compatible = false;

        // Handle PHP specially
        if ($package === 'php') {
            $installedVersion = PHP_VERSION;
            $status = 'installed';
            $compatible = self::isVersionCompatible($installedVersion, $requiredVersion);
            $status = $compatible ? 'compatible' : 'incompatible';
        } elseif (isset($installed[$package])) {
            $installedVersion = $installed[$package]['version'];
            $status = 'installed';
            
            try {
                $compatible = self::isVersionCompatible($installedVersion, $requiredVersion);
                if ($compatible) {
                    $status = 'compatible';
                } else {
                    $status = 'incompatible';
                }
            } catch (Exception $e) {
                $status = 'version_error';
            }
        }

        return [
            'name' => $package,
            'required_version' => $requiredVersion,
            'installed_version' => $installedVersion,
            'status' => $status,
            'compatible' => $compatible,
            'is_dev' => $isDev
        ];
    }

    private static function getInstalledPackages()
    {
        $lockPath = base_path('composer.lock');
        
        if (!file_exists($lockPath)) {
            return [];
        }

        $lock = json_decode(file_get_contents($lockPath), true);
        $packages = [];

        // Process main packages
        if (isset($lock['packages'])) {
            foreach ($lock['packages'] as $package) {
                $packages[$package['name']] = [
                    'version' => $package['version'],
                    'type' => $package['type'] ?? 'library'
                ];
            }
        }

        // Process dev packages
        if (isset($lock['packages-dev'])) {
            foreach ($lock['packages-dev'] as $package) {
                $packages[$package['name']] = [
                    'version' => $package['version'],
                    'type' => $package['type'] ?? 'library',
                    'dev' => true
                ];
            }
        }

        return $packages;
    }

    private static function isVersionCompatible($installed, $required)
    {
        if ($required === '*') {
            return true;
        }

        // Clean version strings
        $cleanInstalled = ltrim($installed, 'v');
        
        // Handle OR conditions (e.g., ^9.0|^10.0|^11.0)
        if (strpos($required, '|') !== false) {
            $constraints = explode('|', $required);
            foreach ($constraints as $constraint) {
                if (self::checkSingleConstraint($cleanInstalled, trim($constraint))) {
                    return true;
                }
            }
            return false;
        }
        
        return self::checkSingleConstraint($cleanInstalled, $required);
    }
    
    private static function checkSingleConstraint($installed, $constraint)
    {
        // Handle dev versions (e.g., 2.x-dev should match ^2.0)
        if (strpos($installed, '-dev') !== false) {
            $devVersion = str_replace(['.x-dev', '-dev'], '', $installed);
            $installed = $devVersion . '.999'; // Treat as high version in that branch
        }
        
        $cleanConstraint = ltrim($constraint, '^~>=<');
        
        if (strpos($constraint, '^') === 0) {
            // Caret constraint: ^2.0 means >=2.0.0 <3.0.0
            $parts = explode('.', $cleanConstraint);
            $majorVersion = $parts[0];
            $nextMajor = ($majorVersion + 1) . '.0.0';
            
            return version_compare($installed, $cleanConstraint, '>=') && 
                   version_compare($installed, $nextMajor, '<');
        } elseif (strpos($constraint, '~') === 0) {
            // Tilde constraint: ~1.2.3 means >=1.2.3 <1.3.0
            return version_compare($installed, $cleanConstraint, '>=');
        } elseif (strpos($constraint, '>=') === 0) {
            // >= constraint
            return version_compare($installed, $cleanConstraint, '>=');
        } else {
            // Exact comparison
            return version_compare($installed, $cleanConstraint, '>=');
        }
    }

    private static function getComposerVersion()
    {
        try {
            $output = shell_exec('composer --version 2>&1');
            if (preg_match('/Composer version ([^\s]+)/', $output, $matches)) {
                return $matches[1];
            }
        } catch (Exception $e) {
            // Ignore
        }
        
        return 'unknown';
    }

    public static function checkCriticalDependencies()
    {
        $critical = [
            'php' => PHP_VERSION,
            'laravel/framework' => '^9.0|^10.0|^11.0'
        ];

        $results = [];
        $installed = self::getInstalledPackages();

        foreach ($critical as $package => $version) {
            if ($package === 'php') {
                $results[] = [
                    'name' => 'php',
                    'required_version' => '>=8.1',
                    'installed_version' => PHP_VERSION,
                    'status' => version_compare(PHP_VERSION, '8.1.0', '>=') ? 'compatible' : 'incompatible',
                    'critical' => true
                ];
            } else {
                $result = self::checkPackage($package, $version, $installed);
                $result['critical'] = true;
                $results[] = $result;
            }
        }

        return $results;
    }
}