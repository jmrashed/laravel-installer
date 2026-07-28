<?php

namespace Tests\Feature;

use Tests\TestCase;

class UpdateControllerTest extends TestCase
{
    protected function markInstalled()
    {
        file_put_contents(storage_path('installed'), '');
    }

    protected function cleanupInstalledFlag()
    {
        if (file_exists(storage_path('installed'))) {
            unlink(storage_path('installed'));
        }
    }

    public function test_welcome_redirects_to_installer_when_not_installed()
    {
        $response = $this->get('/update');

        $response->assertRedirect(route('LaravelInstaller::welcome'));
    }

    public function test_welcome_page_loads_when_installed_and_update_pending()
    {
        $this->markInstalled();

        try {
            $response = $this->get('/update');

            $response->assertStatus(200);
            $response->assertViewIs('vendor.installer.update.welcome');
        } finally {
            $this->cleanupInstalledFlag();
        }
    }

    public function test_overview_page_loads_when_installed_and_update_pending()
    {
        $this->markInstalled();

        try {
            $response = $this->get('/update/overview');

            $response->assertStatus(200);
            $response->assertViewIs('vendor.installer.update.overview');
            $response->assertViewHas('numberOfUpdatesPending');
        } finally {
            $this->cleanupInstalledFlag();
        }
    }

    public function test_database_runs_migration_and_redirects_to_final()
    {
        $this->markInstalled();

        try {
            $response = $this->get('/update/database');

            $response->assertRedirect(route('LaravelUpdater::final'));
            $response->assertSessionHas('message');
        } finally {
            $this->cleanupInstalledFlag();
        }
    }

    public function test_finish_writes_installed_file_and_shows_finished_view()
    {
        try {
            $response = $this->withSession(['message' => ['message' => 'Update complete.']])
                ->get('/update/final');

            $response->assertStatus(200);
            $response->assertViewIs('vendor.installer.update.finished');
            $this->assertFileExists(storage_path('installed'));
        } finally {
            $this->cleanupInstalledFlag();
        }
    }

    public function test_routes_blocked_when_updater_disabled()
    {
        config(['installer.updaterEnabled' => 'false']);
        $this->markInstalled();

        try {
            $response = $this->get('/update');
            $response->assertStatus(404);
        } finally {
            $this->cleanupInstalledFlag();
        }
    }
}
