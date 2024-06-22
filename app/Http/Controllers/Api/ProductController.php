<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Tenants;

class ProductController extends Controller
{
    public function pushProduct(Request $request)
    {
        try {
            $requestData = $request->all();

            if (!isset($requestData['product']['kode'])) {
            	return response()->json(['error' => 'kode tidak boleh kosong'], 500);
            }

            if (!isset($requestData['product']['nama'])) {
            	return response()->json(['error' => 'nama tidak boleh kosong'], 500);
            }

            $sku = $requestData['product']['kode'];

            $product = Product::updateOrCreate(
                ['sku' => $sku],
                ['data' => $requestData]
            );

            if ($product->shop_product_id !== null) {
            	$this->updateProductToShopify($product);
            } else {
            	//$existSku = $this->checkSkuExistInShopify($product);

            	$this->createProductToShopify($product);
            }

            return response()->json(['message' => 'Product saved successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateProductToShopify($product)
    {
        $existingTenants = Tenants::first();

        if ($existingTenants) {
            $accessToken = $existingTenants->token;
            $shop = $existingTenants->domain;

            $id = $product->shop_product_id;
            $domain = $existingTenants->domain;
            $token = $existingTenants->token;

            $data = json_decode($product->data, true);
            $data['product']['id'] = (int)$id;

            // Make the API request to update the product
            $response = $this->callRestApi($domain, $token, "/admin/api/2024-04/products/{$id}.json", $data, 'PUT');

            return $response;
        }

        return redirect()->route('products')->with('success', 'Product title updated successfully.');
    }

    public function createProductToShopify($product)
    {
        $existingTenants = Tenants::first();

        if ($existingTenants) {
            $accessToken = $existingTenants->token;
            $shop = $existingTenants->domain;
            $domain = $existingTenants->domain;
            $token = $existingTenants->token;

            $data = $product->data;

            // Make the API request to update the product
            $response = $this->callRestApi($domain, $token, "/admin/api/2024-04/products/{$id}.json", $data, 'PUT');
            $response = $this->callRestApi($domain, $token, '/admin/api/2021-04/products.json', $data, 'POST');

            return $response;
        }

        return redirect()->route('products')->with('success', 'Product title updated successfully.');
    }

    public function callRestApi($domain, $token, $endpoint, $query = [], $method = 'GET')
    {
        $url = 'https://' . $shopUrl . $endpoint;

        $options = [
            'headers' => [
                'X-Shopify-Access-Token' => $accessToken,
            ],
            'timeout' => 60,
            'connect_timeout' => 60,
        ];

        if (in_array($method, ['GET', 'DELETE']) && !empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        if (in_array($method, ['POST', 'PUT'])) {
            $options['json'] = $query;
            $response = Http::withHeaders($options['headers'])->send($method, $url, ['json' => $query]);
        }

        if (!isset($response)) {
            $response = Http::withOptions($options)->send($method, $url);
        }

        return [
            'headers' => $response->headers(),
            'body' => json_decode($response->body(), true),
        ];
    }
}