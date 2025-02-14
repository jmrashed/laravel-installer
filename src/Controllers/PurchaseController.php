<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    /**
     * Main method to handle the Envato purchase validation process.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function validatePurchase(Request $request)
    {
        // Validate the incoming request
        $this->validateRequest($request);

        // Retrieve Envato access token from a local API
        $accessToken = $this->getEnvatoAccessToken();
        if (!$accessToken) {
            return response()->json(['message' => 'Envato access token not found.'], 400);
        }

        // Validate the purchase code with Envato API
        $isValid = $this->validatePurchaseCode($accessToken, $request->purchase_code);
        if ($isValid) {
            $this->createPurchaseVerifiedFile($request->purchase_code);
         
            return redirect('install/purchase-validation?message="valid"');
        }

        return redirect('install/purchase-validation?message="invalid"')->withInput()->with('message', 'Purchase code is invalid.');
    }

    /**
     * Validate the incoming request data.
     *
     * @param Request $request
     */
    private function validateRequest(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string',
            'domain' => 'nullable|string',
            'email' => 'nullable|email|string',
        ]);
    }

    /**
     * Retrieve the Envato access token from a local API.
     *
     * @return string|null
     */
    private function getEnvatoAccessToken()
    {
        $envatoApiTokenUrl = 'http://127.0.0.1:8089/api/get-barrier-token';

        try {
            $tokenResponse = Http::get($envatoApiTokenUrl);
            if ($tokenResponse->successful()) {
                $tokenData = $tokenResponse->json();
                return $tokenData['onesttech']['token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Envato access token: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Validate the purchase code with Envato API.
     *
     * @param string $accessToken
     * @param string $purchaseCode
     * @return bool
     */
    private function validatePurchaseCode(string $accessToken, string $purchaseCode)
    {
        $envatoApiUrl = 'https://api.envato.com/v3/market/author/sale';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($envatoApiUrl, [
                'code' => $purchaseCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['item']) && isset($data['item']['id'])) {
                   
                     $this->storeClients($data);
                }
                return isset($data['item']) && isset($data['item']['id']);
            }
        } catch (\Exception $e) {
            Log::error('Error contacting Envato API: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Create a .purchase-verified file with the purchase code.
     *
     * @param string $purchaseCode
     */
    private function createPurchaseVerifiedFile(string $purchaseCode)
    {
        $filePath = base_path('.purchase-verified');
        File::put($filePath, $purchaseCode);
    }
    public function storeClients($request)
    {
        $url = 'http://127.0.0.1:8089/api/store-envato-verification-response';
    
        try {
            // Sending the POST request to the specified URL
            $inputProcess = json_encode($request);
            $response = Http::post($url, $inputProcess);
    
            // Check if the response is successful
            if ($response->successful()) {
                return true;
            }
    
            // Log an error if the response is not successful
            Log::error('Error storing client data. Response: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            // Log the error if an exception is thrown
            Log::error('Exception in storeClients method: ' . $e->getMessage());
            return false;
        }
    }
    
}
