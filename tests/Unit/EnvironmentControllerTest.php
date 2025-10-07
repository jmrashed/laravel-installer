<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Jmrashed\LaravelInstaller\Controllers\EnvironmentController;
use Jmrashed\LaravelInstaller\Helpers\EnvironmentManager;
use Jmrashed\LaravelInstaller\Helpers\SecurityHelper;
use Jmrashed\LaravelInstaller\Helpers\BackupManager;

class EnvironmentControllerTest extends TestCase
{
    protected $controller;
    protected $environmentManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->environmentManager = $this->createMock(EnvironmentManager::class);
        $this->controller = new EnvironmentController($this->environmentManager);
    }

    public function testSaveClassicWithValidInput()
    {
        $request = new Request(['envConfig' => 'APP_NAME="Test App"']);
        
        $this->environmentManager
            ->expects($this->once())
            ->method('saveFileClassic')
            ->willReturn('Success message');

        $response = $this->controller->saveClassic($request, redirect());
        
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testSaveWizardWithInvalidTab()
    {
        $request = new Request(['tab' => 'invalid_tab']);
        
        $response = $this->controller->saveWizard($request, redirect());
        
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testRateLimitingPreventsExcessiveRequests()
    {
        RateLimiter::shouldReceive('tooManyAttempts')
            ->with('env-save:127.0.0.1', 5)
            ->andReturn(true);

        $request = new Request(['envConfig' => 'APP_NAME="Test"']);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        
        $response = $this->controller->saveClassic($request, redirect());
        
        $this->assertTrue($response->getSession()->has('errors'));
    }

    public function testSecurityHelperSanitization()
    {
        $maliciousInput = '<script>alert("xss")</script>';
        $sanitized = SecurityHelper::sanitizeInput($maliciousInput);
        
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringNotContainsString('alert', $sanitized);
    }

    public function testValidationRulesAreApplied()
    {
        $request = new Request([
            'tab' => 'configuration',
            'app_name' => '',
            'environment' => 'production',
            'app_debug' => 'false',
            'app_log_level' => 'error',
            'app_url' => 'invalid-url'
        ]);

        $response = $this->controller->saveWizard($request, redirect());
        
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }
}