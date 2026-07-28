<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Jmrashed\LaravelInstaller\Helpers\MigrationsHelper;

class MigrationsHelperTest extends TestCase
{
    public function test_get_migrations_returns_migration_files_without_extension()
    {
        $migrationDir = database_path('migrations');
        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0755, true);
        }
        $testMigration = $migrationDir . '/9999_99_99_999999_dummy_migration.php';
        file_put_contents($testMigration, '<?php // dummy');

        try {
            $helper = $this->makeHelper();
            $migrations = $helper->getMigrations();

            $this->assertIsArray($migrations);
            $this->assertContains(
                database_path('migrations') . '/9999_99_99_999999_dummy_migration',
                $migrations
            );
            foreach ($migrations as $migration) {
                $this->assertStringNotContainsString('.php', $migration);
            }
        } finally {
            if (file_exists($testMigration)) {
                unlink($testMigration);
            }
        }
    }

    public function test_get_executed_migrations_returns_collection_from_migrations_table()
    {
        DB::table('migrations')->insert([
            'migration' => '2024_01_01_000000_create_test_table',
            'batch' => 1,
        ]);

        try {
            $helper = $this->makeHelper();
            $executed = $helper->getExecutedMigrations();

            $this->assertInstanceOf(\Illuminate\Support\Collection::class, $executed);
            $this->assertContains('2024_01_01_000000_create_test_table', $executed);
        } finally {
            DB::table('migrations')->where('migration', '2024_01_01_000000_create_test_table')->delete();
        }
    }

    private function makeHelper()
    {
        return new class {
            use MigrationsHelper;
        };
    }
}
