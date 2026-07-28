<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\DatabaseOptimizer;

class DatabaseOptimizerTest extends TestCase
{
    private $originalMemoryLimit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalMemoryLimit = ini_get('memory_limit');
    }

    protected function tearDown(): void
    {
        ini_set('memory_limit', $this->originalMemoryLimit);
        parent::tearDown();
    }

    public function test_optimize_for_large_database_is_noop_for_unsupported_driver()
    {
        config(['database.default' => 'testing']);

        // sqlite isn't in the mysql/pgsql statement map, so this should run without error.
        DatabaseOptimizer::optimizeForLargeDatabase();

        $this->assertTrue(true);
    }

    public function test_run_migrations_in_batches_completes_without_error()
    {
        DatabaseOptimizer::runMigrationsInBatches(5);

        $this->assertTrue(true);
    }

    public function test_optimize_memory_usage_runs_without_error()
    {
        DatabaseOptimizer::optimizeMemoryUsage();

        $this->assertTrue(true);
    }

    public function test_parse_memory_limit_converts_shorthand_notation()
    {
        $method = $this->getPrivateMethod('parseMemoryLimit');

        $this->assertEquals(256 * 1024 * 1024, $method->invoke(null, '256M'));
        $this->assertEquals(1024 * 1024 * 1024, $method->invoke(null, '1G'));
        $this->assertEquals(512 * 1024, $method->invoke(null, '512K'));
    }

    public function test_format_memory_limit_produces_shorthand_notation()
    {
        $method = $this->getPrivateMethod('formatMemoryLimit');

        $this->assertEquals('1G', $method->invoke(null, 1024 * 1024 * 1024));
        $this->assertEquals('256M', $method->invoke(null, 256 * 1024 * 1024));
        $this->assertEquals('512K', $method->invoke(null, 512 * 1024));
    }

    public function test_calculate_optimal_memory_limit_doubles_the_configured_limit()
    {
        $method = $this->getPrivateMethod('calculateOptimalMemoryLimit');

        $result = $method->invoke(null, 128 * 1024 * 1024);

        $this->assertSame(256 * 1024 * 1024, $result);
    }

    public function test_calculate_optimal_memory_limit_returns_null_when_already_unlimited()
    {
        $method = $this->getPrivateMethod('calculateOptimalMemoryLimit');

        $result = $method->invoke(null, -1);

        $this->assertNull($result);
    }

    private function getPrivateMethod($name)
    {
        $reflection = new \ReflectionClass(DatabaseOptimizer::class);
        $method = $reflection->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
