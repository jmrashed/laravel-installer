<?php

namespace Tests\Unit;

use Jmrashed\LaravelInstaller\Helpers\RequirementsChecker;
use Tests\TestCase;

class RequirementsCheckerTest extends TestCase
{
    public function testCheckPHPRequirements()
    {
        $checker = new RequirementsChecker();

        $requirements = [
            'php' => ['pdo', 'mbstring', 'openssl']
        ];

        $result = $checker->check($requirements);

        $this->assertArrayHasKey('requirements', $result);
        $this->assertArrayHasKey('php', $result['requirements']);

        foreach ($requirements['php'] as $extension) {
            $this->assertArrayHasKey($extension, $result['requirements']['php']);
            $this->assertIsBool($result['requirements']['php'][$extension]);
        }
    }

    public function testCheckPHPVersionSupported()
    {
        $checker = new RequirementsChecker();

        $result = $checker->checkPHPversion('8.1.0');

        $this->assertArrayHasKey('full', $result);
        $this->assertArrayHasKey('current', $result);
        $this->assertArrayHasKey('minimum', $result);
        $this->assertArrayHasKey('supported', $result);
        $this->assertEquals('8.1.0', $result['minimum']);
        $this->assertIsBool($result['supported']);
    }

    public function testCheckPHPVersionNotSupported()
    {
        $checker = new RequirementsChecker();

        $result = $checker->checkPHPversion('9.0.0');

        $this->assertArrayHasKey('supported', $result);
        $this->assertFalse($result['supported']);
    }

    public function testCheckPHPVersionWithoutParameter()
    {
        $checker = new RequirementsChecker();

        $result = $checker->checkPHPversion();

        $this->assertArrayHasKey('minimum', $result);
        $this->assertEquals('7.0.0', $result['minimum']); // default min version
    }

    public function testGetMinPhpVersion()
    {
        $checker = new RequirementsChecker();

        $reflection = new \ReflectionClass($checker);
        $method = $reflection->getMethod('getMinPhpVersion');
        $method->setAccessible(true);

        $minVersion = $method->invoke($checker);

        $this->assertEquals('7.0.0', $minVersion);
    }

    public function testGetPhpVersionInfo()
    {
        $versionInfo = RequirementsChecker::getPhpVersionInfo();

        $this->assertArrayHasKey('full', $versionInfo);
        $this->assertArrayHasKey('version', $versionInfo);
        $this->assertIsString($versionInfo['full']);
        $this->assertIsString($versionInfo['version']);
        $this->assertEquals(PHP_VERSION, $versionInfo['full']);
    }
}