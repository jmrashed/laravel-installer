<?php

namespace Tests\Feature;

use Tests\TestCase;

class InstallerControllerTest extends TestCase
{
    public function test_welcome_page_loads()
    {
        $response = $this->get('/install');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.welcome');
    }

    public function test_purchase_validation_page_loads()
    {
        $response = $this->get('/install/purchase-validation');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.purchase-validation');
    }

    public function test_server_requirements_page_loads()
    {
        $response = $this->get('/install/server-requirements');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.server-requirements');
        $response->assertViewHasAll(['requirements', 'phpSupportInfo']);
    }

    public function test_permissions_page_loads()
    {
        $response = $this->get('/install/permissions');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.permissions');
        $response->assertViewHas('permissions');
    }

    public function test_environment_setting_page_loads()
    {
        $response = $this->get('/install/environment-setting');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.environment-setting');
    }

    public function test_configuration_setting_page_loads()
    {
        $response = $this->get('/install/configuration-setting');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.configuration-setting');
    }

    public function test_database_setting_page_loads()
    {
        $response = $this->get('/install/database-setting');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.database-setting');
    }

    public function test_application_setting_page_loads()
    {
        $response = $this->get('/install/application-setting');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.application-setting');
    }

    public function test_classic_text_editor_page_loads_and_reflects_current_env_file()
    {
        $envPath = base_path('.env');
        $expected = file_exists($envPath) ? file_get_contents($envPath) : '';

        $response = $this->get('/install/classic-text-editor');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.classic-text-editor');
        $response->assertViewHas('envContent', $expected);
    }

    public function test_classic_text_editor_page_loads_with_env_file_contents()
    {
        $envPath = base_path('.env');
        $existed = file_exists($envPath);
        $original = $existed ? file_get_contents($envPath) : null;

        file_put_contents($envPath, 'APP_NAME=Test');

        try {
            $response = $this->get('/install/classic-text-editor');

            $response->assertStatus(200);
            $response->assertViewHas('envContent', 'APP_NAME=Test');
        } finally {
            if ($existed) {
                file_put_contents($envPath, $original);
            } elseif (file_exists($envPath)) {
                unlink($envPath);
            }
        }
    }

    public function test_installation_finished_page_loads()
    {
        $response = $this->get('/install/installation-finished');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.installation-finished');
    }

    public function test_dependencies_page_loads()
    {
        $response = $this->get('/install/dependencies');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.dependencies');
    }

    public function test_performance_dashboard_page_loads()
    {
        $response = $this->get('/install/performance-dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.performance-dashboard');
    }

    public function test_cache_queue_page_loads()
    {
        $response = $this->get('/install/cache-queue');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.cache-queue');
    }

    public function test_database_backup_page_loads()
    {
        $response = $this->get('/install/database-backup');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.database-backup');
    }

    public function test_resume_installation_page_loads()
    {
        $response = $this->get('/install/resume-installation');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.resume-installation');
    }

    public function test_welcome_page_blocked_when_already_installed()
    {
        file_put_contents(storage_path('installed'), '');

        try {
            $response = $this->get('/install');
            $response->assertStatus(404);
        } finally {
            if (file_exists(storage_path('installed'))) {
                unlink(storage_path('installed'));
            }
        }
    }
}
