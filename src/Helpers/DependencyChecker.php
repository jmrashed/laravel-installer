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

        if (isset($installed[$package])) {
            $installedVersion = $installed[$package]['version'];
            $status = 'installed';
            
            try {
                $compatible = self::isVersionCompatible($installedVersion, $requiredVersion);
                if (!$compatible) {
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

        // Simple version comparison without Composer dependencies
        $cleanRequired = ltrim($required, '^~>=<');
        $cleanRequired = preg_replace('/\|.*$/', '', $cleanRequired); // Remove OR conditions
        
        if (strpos($required, '^') === 0) {
            // Caret constraint: ^1.2.3 means >=1.2.3 <2.0.0
            return version_compare($installed, $cleanRequired, '>=');
        } elseif (strpos($required, '~') === 0) {
            // Tilde constraint: ~1.2.3 means >=1.2.3 <1.3.0
            return version_compare($installed, $cleanRequired, '>=');
        } else {
            // Exact or >= comparison
            return version_compare($installed, $cleanRequired, '>=');
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
                $results[] = self::checkPackage($package, $version, $installed);
            }
        }

        return $results;
    }
}