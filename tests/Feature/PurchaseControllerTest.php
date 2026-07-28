<?php

namespace Tests\Feature;

use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    /**
     * validatePurchase() currently returns an early `redirect('install/purchase-validation?message="valid"')`
     * before any validation or Envato API calls are made, so the request always succeeds regardless of
     * input. The tests below document this actual behaviour without exercising the unreachable code paths.
     */
    public function test_validate_purchase_redirects_as_valid_without_purchase_code()
    {
        $response = $this->post('/install/validate-purchase', []);

        $response->assertRedirect('install/purchase-validation?message="valid"');
    }

    public function test_validate_purchase_redirects_as_valid_with_purchase_code()
    {
        $response = $this->post('/install/validate-purchase', [
            'purchase_code' => 'some-purchase-code',
        ]);

        $response->assertRedirect('install/purchase-validation?message="valid"');
    }
}
