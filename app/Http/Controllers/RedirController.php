<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenants;

class RedirController extends Controller
{
	static $api_key = '47c9560fdeac068b9770862fa3cbf034';
	static $secret_key = '8751f73c117c0334ae07db30dd3bec2b';

    public function index(Request $request)
	{
		$token = $request->input('code');
		$hmac = $request->input('hmac');
		$shopUrl = $request->input('shop');
		$parameters = array_diff_key($request->all(), array('hmac' => ''));
		ksort($parameters);

		$newHmac = hash_hmac('sha256', http_build_query($parameters), self::$secret_key);

		if (hash_equals($hmac, $newHmac)) {
			

			$accessTokenEndpoint = 'https://' . $shopUrl . '/admin/oauth/access_token';
			$var = [
				'client_id' => self::$api_key,
				'client_secret' => self::$secret_key,
				'code' => $token
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $accessTokenEndpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, count($var));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($var));
			$response = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($response);

			$shop = Tenants::updateOrCreate(
			    ['domain' => $shopUrl], // Search condition: If a shop with this URL exists
			    ['token' => $response->access_token] // Data to update or create
			);

			return redirect(url('/'));

		} else {
			return response()->json(['message' => 'Shop data cannot saved'], 201);
		}
	}
}