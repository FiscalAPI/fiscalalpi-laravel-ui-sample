<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::resource('products', ProductController::class);
Route::get('/', [HomeController::class, 'index']);
Route::get('/users', [HomeController::class, 'users']);
