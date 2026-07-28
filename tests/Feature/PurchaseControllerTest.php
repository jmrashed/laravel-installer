<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    /**
     * Purchase validation is opt-in (installer.purchase_validation.enabled,
     * default false). With it off, every request short-circuits to "valid"
     * without hitting the network - this is the historical default and
     * remains the out-of-the-box behaviour for consumers who don't sell
     * through Envato.
     */
    public function test_validate_purchase_redirects_as_valid_when_disabled_without_purchase_code()
    {
        $response = $this->post('/install/validate-purchase', []);

        $response->assertRedirect('install/purchase-validation?message="valid"');
    }

    public function test_validate_purchase_redirects_as_valid_when_disabled_with_purchase_code()
    {
        $response = $this->post('/install/validate-purchase', [
            'purchase_code' => 'some-purchase-code',
        ]);

        $response->assertRedirect('install/purchase-validation?message="valid"');
    }

    /**
     * With purchase validation enabled, a missing purchase_code must now
     * actually fail validation instead of being silently accepted - this is
     * the regression test for the previous unconditional bypass.
     */
    public function test_validate_purchase_rejects_missing_purchase_code_when_enabled()
    {
        config(['installer.purchase_validation.enabled' => true]);

        $response = $this->post('/install/validate-purchase', []);

        $response->assertSessionHasErrors('purchase_code');
    }

    public function test_validate_purchase_rejects_invalid_code_when_enabled()
    {
        config(['installer.purchase_validation.enabled' => true]);

        Http::fake([
            '127.0.0.1:8089/*' => Http::response(['onesttech' => ['token' => 'test-token']], 200),
            'api.envato.com/*' => Http::response(['error' => 'invalid_code'], 200),
        ]);

        $response = $this->post('/install/validate-purchase', [
            'purchase_code' => 'not-a-real-code',
        ]);

        $response->assertRedirect('install/purchase-validation?message="invalid"');
    }

    public function test_validate_purchase_accepts_valid_code_when_enabled()
    {
        config(['installer.purchase_validation.enabled' => true]);

        Http::fake([
            '127.0.0.1:8089/*' => Http::response(['onesttech' => ['token' => 'test-token']], 200),
            'api.envato.com/*' => Http::response(['item' => ['id' => 123]], 200),
        ]);

        $response = $this->post('/install/validate-purchase', [
            'purchase_code' => 'a-real-purchase-code',
        ]);

        $response->assertRedirect('install/purchase-validation?message="valid"');
        $this->assertFileExists(base_path('.purchase-verified'));
        unlink(base_path('.purchase-verified'));
    }
}
