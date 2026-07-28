<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class HelperFunctionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/isactive-test-route', function () {
            return 'ok';
        })->name('isactive.test.route');
    }

    public function test_is_active_returns_class_name_for_matching_route()
    {
        $this->get('/isactive-test-route');

        $this->assertEquals('active', isActive('isactive.test.route'));
    }

    public function test_is_active_returns_empty_for_non_matching_route()
    {
        $this->get('/isactive-test-route');

        $this->assertEmpty(isActive('some.other.route'));
    }

    public function test_is_active_supports_custom_class_name()
    {
        $this->get('/isactive-test-route');

        $this->assertEquals('current', isActive('isactive.test.route', 'current'));
    }

    public function test_is_active_supports_array_of_routes()
    {
        $this->get('/isactive-test-route');

        $this->assertEquals('active', isActive(['other.route', 'isactive.test.route']));
        $this->assertEmpty(isActive(['other.route', 'another.route']));
    }
}
