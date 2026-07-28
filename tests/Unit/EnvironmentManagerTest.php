<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Helpers\EnvironmentManager;

class EnvironmentManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            unlink($envPath);
        }

        $envExamplePath = base_path('.env.example');
        if (file_exists($envExamplePath)) {
            unlink($envExamplePath);
        }

        foreach (glob(base_path('.env.backup.*')) as $backup) {
            unlink($backup);
        }

        foreach (glob(base_path('.env.tmp')) as $tmp) {
            unlink($tmp);
        }

        parent::tearDown();
    }

    public function test_get_env_path_and_example_path()
    {
        $manager = new EnvironmentManager();

        $this->assertEquals(base_path('.env'), $manager->getEnvPath());
        $this->assertEquals(base_path('.env.example'), $manager->getEnvExamplePath());
    }

    public function test_get_env_content_creates_file_from_example()
    {
        if (file_exists(base_path('.env'))) {
            unlink(base_path('.env'));
        }
        file_put_contents(base_path('.env.example'), 'APP_NAME=Example');

        $manager = new EnvironmentManager();
        $content = $manager->getEnvContent();

        $this->assertEquals('APP_NAME=Example', $content);
        $this->assertFileExists(base_path('.env'));
    }

    public function test_get_env_content_touches_empty_file_when_no_example()
    {
        if (file_exists(base_path('.env'))) {
            unlink(base_path('.env'));
        }
        if (file_exists(base_path('.env.example'))) {
            unlink(base_path('.env.example'));
        }

        $manager = new EnvironmentManager();
        $content = $manager->getEnvContent();

        $this->assertEquals('', $content);
        $this->assertFileExists(base_path('.env'));
    }

    public function test_get_env_content_returns_existing_content()
    {
        file_put_contents(base_path('.env'), 'APP_NAME=Existing');

        $manager = new EnvironmentManager();

        $this->assertEquals('APP_NAME=Existing', $manager->getEnvContent());
    }

    public function test_save_file_classic_writes_valid_config()
    {
        $manager = new EnvironmentManager();
        $request = Request::create('/install/environment/saveClassic', 'POST', [
            'envConfig' => "APP_NAME=\"Test App\"\nAPP_ENV=\"local\"",
        ]);

        $message = $manager->saveFileClassic($request);

        $this->assertEquals(trans('installer_messages.environment.success'), $message);
        $this->assertStringEqualsFile(base_path('.env'), "APP_NAME=\"Test App\"\nAPP_ENV=\"local\"");
    }

    public function test_save_file_classic_rejects_dangerous_config()
    {
        $manager = new EnvironmentManager();
        $request = Request::create('/install/environment/saveClassic', 'POST', [
            'envConfig' => 'APP_NAME=Test<?php system("rm -rf /"); ?>',
        ]);

        $message = $manager->saveFileClassic($request);

        $this->assertEquals(trans('installer_messages.environment.errors'), $message);
    }

    public function test_save_file_wizard_updates_configuration_tab()
    {
        file_put_contents(base_path('.env'), "APP_NAME=Old\nAPP_ENV=local\n");

        $manager = new EnvironmentManager();
        $request = Request::create('/install/environment/saveWizard', 'POST', [
            'tab' => 'configuration',
            'app_name' => 'New App',
            'environment' => 'production',
            'app_debug' => 'false',
            'app_log_level' => 'error',
            'app_url' => 'https://example.com',
        ]);

        $message = $manager->saveFileWizard($request);

        $this->assertEquals(trans('installer_messages.environment.success'), $message);
        $envContent = file_get_contents(base_path('.env'));
        $this->assertStringContainsString('APP_NAME="New App"', $envContent);
        $this->assertStringContainsString('APP_URL="https://example.com"', $envContent);
        $this->assertStringContainsString('APP_HTTPS=true', $envContent);
        $this->assertStringContainsString('APP_DOMAIN="example.com"', $envContent);
    }

    public function test_save_file_wizard_rejects_invalid_database_credentials()
    {
        file_put_contents(base_path('.env'), "APP_NAME=Old\n");

        $manager = new EnvironmentManager();
        $request = Request::create('/install/environment/saveWizard', 'POST', [
            'tab' => 'database',
            'database_connection' => 'mysql',
            'database_hostname' => '',
            'database_port' => '99999',
            'database_name' => 'test_db',
            'database_username' => 'root',
            'database_password' => '',
        ]);

        $message = $manager->saveFileWizard($request);

        $this->assertEquals(trans('installer_messages.environment.errors'), $message);
    }

    public function test_save_file_wizard_returns_error_for_unknown_tab()
    {
        file_put_contents(base_path('.env'), "APP_NAME=Old\n");

        $manager = new EnvironmentManager();
        $request = Request::create('/install/environment/saveWizard', 'POST', [
            'tab' => 'unknown',
        ]);

        $message = $manager->saveFileWizard($request);

        $this->assertEquals(trans('installer_messages.environment.errors'), $message);
    }
}
