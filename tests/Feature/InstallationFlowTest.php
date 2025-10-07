<?php

namespace Tests\Feature;

use Tests\TestCase;

class InstallationFlowTest extends TestCase
{
    public function testCompleteInstallationFlow()
    {
        // Test welcome page
        $response = $this->get('/install');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.welcome');

        // Test requirements check
        $response = $this->get('/install/requirements');
        $response->assertStatus(200);

        // Test permissions check
        $response = $this->get('/install/permissions');
        $response->assertStatus(200);

        // Test environment setup
        $response = $this->get('/install/environment');
        $response->assertStatus(200);
    }

    public function testInstallationWithValidData()
    {
        $environmentData = [
            'database_connection' => 'mysql',
            'database_hostname' => 'localhost',
            'database_port' => '3306',
            'database_name' => 'test_db',
            'database_username' => 'root',
            'database_password' => '',
            'app_name' => 'Test App',
            'app_url' => 'http://localhost',
        ];

        $response = $this->post('/install/environment/saveWizard', $environmentData);
        $response->assertRedirect();
    }

    public function testInstallationBlockedWhenAlreadyInstalled()
    {
        // Create installed file
        file_put_contents(storage_path('installed'), '');

        $response = $this->get('/install');
        $response->assertStatus(404);

        // Clean up
        if (file_exists(storage_path('installed'))) {
            unlink(storage_path('installed'));
        }
    }
}