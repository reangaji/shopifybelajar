<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
	static $scopes = "read_products, write_products";
	static $redirectUri = "/redir";
    static $accessMode = '';
    static $api_key = '47c9560fdeac068b9770862fa3cbf034';
    static $ngrok_url = 'https://6f0b-114-10-124-117.ngrok-free.app';

    public function index(Request $request)
    {
        $shop = $request->input('shop');
        $nonce = bin2hex(random_bytes(12));

        $oauthUrl = 'https://'. $shop . '/admin/oauth/authorize?client_id='. self::$api_key . '&scope='. self::$scopes . '&redirect_uri='. urlencode(self::$ngrok_url . self::$redirectUri) .'&state='. $nonce . '&grant_options[]='. self::$accessMode;
    
        return redirect($oauthUrl);
    }
}