<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Add logic to handle the product push
        return response()->json(['message' => 'Product pushed successfully']);
    }
}