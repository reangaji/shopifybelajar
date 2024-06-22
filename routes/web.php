<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\RedirController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('shopify');
});

// Route to connect to InstallController
Route::get('/install', [InstallController::class, 'index'])->name('install.index');

// Route to connect to RedirController
Route::get('/redir', [RedirController::class, 'index'])->name('redir.index');
