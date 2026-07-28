<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\FinalInstallManager;

class FinalInstallManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!file_exists(base_path('.env'))) {
            file_put_contents(base_path('.env'), "APP_NAME=Test\nAPP_KEY=" . config('app.key') . "\n");
        }
    }

    protected function tearDown(): void
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            unlink($envPath);
        }

        parent::tearDown();
    }

    public function test_run_final_generates_key_by_default()
    {
        config(['installer.final.key' => true, 'installer.final.publish' => false]);

        $manager = new FinalInstallManager();
        $output = $manager->runFinal();

        $this->assertIsString($output);
        $this->assertStringContainsString('Application key set successfully', $output);
    }

    public function test_run_final_skips_key_generation_when_disabled()
    {
        config(['installer.final.key' => false, 'installer.final.publish' => false]);

        $manager = new FinalInstallManager();
        $output = $manager->runFinal();

        $this->assertIsString($output);
        $this->assertStringNotContainsString('Application key set successfully', $output);
    }

    public function test_run_final_publishes_vendor_assets_when_enabled()
    {
        config(['installer.final.key' => false, 'installer.final.publish' => true]);

        $manager = new FinalInstallManager();
        $output = $manager->runFinal();

        $this->assertIsString($output);
    }
}
