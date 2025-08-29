<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/', [HomeController::class, 'index']);
Route::get('/users', [HomeController::class, 'users']);
