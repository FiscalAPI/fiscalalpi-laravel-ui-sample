<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PersonController;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::resource('products', ProductController::class);
Route::resource('people', PersonController::class);
Route::get('/', [HomeController::class, 'index']);
Route::get('/users', [HomeController::class, 'users']);

// POS Routes
Route::prefix('pos')->group(function () {
    Route::get('/', [App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/create-order', [App\Http\Controllers\PosController::class, 'createOrder'])->name('pos.create-order');
    Route::post('/add-product', [App\Http\Controllers\PosController::class, 'addProduct'])->name('pos.add-product');
    Route::post('/update-quantity', [App\Http\Controllers\PosController::class, 'updateQuantity'])->name('pos.update-quantity');
    Route::post('/remove-product', [App\Http\Controllers\PosController::class, 'removeProduct'])->name('pos.remove-product');
    Route::post('/update-order', [App\Http\Controllers\PosController::class, 'updateOrder'])->name('pos.update-order');
    Route::post('/cancel-sale', [App\Http\Controllers\PosController::class, 'cancelSale'])->name('pos.cancel-sale');
    Route::post('/end-sale', [App\Http\Controllers\PosController::class, 'endSale'])->name('pos.end-sale');
    Route::get('/search-products', [App\Http\Controllers\PosController::class, 'searchProducts'])->name('pos.search-products');
});
