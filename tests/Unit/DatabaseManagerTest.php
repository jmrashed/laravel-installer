<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\DatabaseManager;

class DatabaseManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        // DatabaseManager::sqlite() calls file_exists()/touch() on the raw database
        // name returned by the connection, which for an in-memory sqlite connection
        // is the literal string ":memory:" - this creates a stray file in the
        // working directory as a side effect of migrateAndSeed().
        $strayFile = base_path(':memory:');
        if (file_exists($strayFile)) {
            unlink($strayFile);
        }
        if (file_exists(':memory:')) {
            unlink(':memory:');
        }

        parent::tearDown();
    }

    public function test_migrate_and_seed_returns_array_response()
    {
        $manager = new DatabaseManager();

        $result = $manager->migrateAndSeed();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('dbOutputLog', $result);
        $this->assertContains($result['status'], ['success', 'error']);
    }

    public function test_migrate_and_seed_runs_migrations()
    {
        $manager = new DatabaseManager();
        $manager->migrateAndSeed();

        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('migrations'));
    }
}
