<?php

namespace Tests\Unit;

use Jmrashed\LaravelInstaller\Helpers\PermissionsChecker;
use Tests\TestCase;

class PermissionsCheckerTest extends TestCase
{
    public function testCheckPermissionsWithWritableFolders()
    {
        $checker = new PermissionsChecker();

        $folders = [
            'storage' => '775',
            'bootstrap/cache' => '775'
        ];

        $result = $checker->check($folders);

        $this->assertArrayHasKey('permissions', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertIsArray($result['permissions']);
        $this->assertCount(2, $result['permissions']);
    }

    public function testCheckPermissionsWithNonWritableFolders()
    {
        $checker = new PermissionsChecker();

        $folders = [
            'nonexistent/folder' => '775'
        ];

        $result = $checker->check($folders);

        $this->assertArrayHasKey('permissions', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertTrue($result['errors']);
        $this->assertCount(1, $result['permissions']);

        $permission = $result['permissions'][0];
        $this->assertArrayHasKey('folder', $permission);
        $this->assertArrayHasKey('permission', $permission);
        $this->assertArrayHasKey('isSet', $permission);
        $this->assertEquals('nonexistent/folder', $permission['folder']);
        $this->assertEquals('775', $permission['permission']);
        $this->assertFalse($permission['isSet']);
    }

    public function testConstructorInitializesResults()
    {
        $checker = new PermissionsChecker();

        $reflection = new \ReflectionClass($checker);
        $property = $reflection->getProperty('results');
        $property->setAccessible(true);

        $results = $property->getValue($checker);

        $this->assertArrayHasKey('permissions', $results);
        $this->assertArrayHasKey('errors', $results);
        $this->assertIsArray($results['permissions']);
        $this->assertNull($results['errors']);
    }

    public function testGetPermissionReturnsString()
    {
        $checker = new PermissionsChecker();

        $reflection = new \ReflectionClass($checker);
        $method = $reflection->getMethod('getPermission');
        $method->setAccessible(true);

        $permission = $method->invoke($checker, 'storage');

        $this->assertIsString($permission);
        $this->assertMatchesRegularExpression('/^\d{4}$/', $permission);
    }
}