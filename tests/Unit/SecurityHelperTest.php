<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\SecurityHelper;

class SecurityHelperTest extends TestCase
{
    public function test_sanitize_input_removes_dangerous_content()
    {
        $maliciousInput = '<script>alert("xss")</script>';
        $sanitized = SecurityHelper::sanitizeInput($maliciousInput);
        
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringNotContainsString('alert', $sanitized);
    }

    public function test_sanitize_input_handles_arrays()
    {
        $input = [
            'safe' => 'normal text',
            'dangerous' => '<script>evil()</script>'
        ];
        
        $sanitized = SecurityHelper::sanitizeInput($input);
        
        $this->assertEquals('normal text', $sanitized['safe']);
        $this->assertStringNotContainsString('<script>', $sanitized['dangerous']);
    }

    public function test_validate_database_credentials_with_valid_data()
    {
        $credentials = [
            'database_hostname' => 'localhost',
            'database_port' => '3306',
            'database_name' => 'test_db',
            'database_username' => 'user'
        ];
        
        $this->assertTrue(SecurityHelper::validateDatabaseCredentials($credentials));
    }

    public function test_validate_database_credentials_with_invalid_data()
    {
        $credentials = [
            'database_hostname' => '',
            'database_port' => '99999',
            'database_name' => 'test_db'
        ];
        
        $this->assertFalse(SecurityHelper::validateDatabaseCredentials($credentials));
    }

    public function test_validate_environment_config_detects_dangerous_code()
    {
        $dangerousConfig = 'APP_NAME=Test<?php system("rm -rf /"); ?>';
        
        $this->assertFalse(SecurityHelper::validateEnvironmentConfig($dangerousConfig));
    }

    public function test_validate_environment_config_allows_safe_content()
    {
        $safeConfig = 'APP_NAME="My Application"' . PHP_EOL . 'APP_ENV=production';
        
        $this->assertTrue(SecurityHelper::validateEnvironmentConfig($safeConfig));
    }
}