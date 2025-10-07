<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\DependencyChecker;

class DependencyCheckerTest extends TestCase
{
    public function test_check_critical_dependencies()
    {
        $critical = DependencyChecker::checkCriticalDependencies();
        
        $this->assertIsArray($critical);
        $this->assertNotEmpty($critical);
        
        $phpDep = collect($critical)->firstWhere('name', 'php');
        $this->assertNotNull($phpDep);
        $this->assertEquals(PHP_VERSION, $phpDep['installed_version']);
    }

    public function test_check_composer_dependencies_structure()
    {
        // Create mock composer.json
        $composerPath = base_path('composer.json');
        if (!file_exists($composerPath)) {
            file_put_contents($composerPath, json_encode([
                'require' => ['php' => '^8.1'],
                'require-dev' => ['phpunit/phpunit' => '^9.0']
            ]));
        }
        
        $dependencies = DependencyChecker::checkComposerDependencies();
        
        $this->assertArrayHasKey('composer_version', $dependencies);
        $this->assertArrayHasKey('php_version', $dependencies);
        $this->assertArrayHasKey('dependencies', $dependencies);
        $this->assertArrayHasKey('dev_dependencies', $dependencies);
    }

    public function test_version_compatibility_check()
    {
        $reflection = new \ReflectionClass(DependencyChecker::class);
        $method = $reflection->getMethod('isVersionCompatible');
        $method->setAccessible(true);
        
        $this->assertTrue($method->invoke(null, '8.1.0', '^8.1'));
        $this->assertFalse($method->invoke(null, '8.0.0', '^8.1'));
        $this->assertTrue($method->invoke(null, '1.0.0', '*'));
    }
}