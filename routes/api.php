<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Test route for orders
Route::get('/orders/test', function () {
    return response()->json(['message' => 'Orders API is working']);
});

// Sales API Routes - No authentication required for now
Route::prefix('orders')->group(function () {
    Route::post('/{id}/generate-invoice', [SalesController::class, 'generateInvoice']);
});

Route::prefix('invoices')->group(function () {
    Route::get('/{invoiceId}/pdf', [SalesController::class, 'getInvoicePdf']);
    Route::get('/{invoiceId}/xml', [SalesController::class, 'getInvoiceXml']);
    Route::post('/{invoiceId}/send-email', [SalesController::class, 'sendInvoiceByEmail']);
    Route::get('/{invoiceId}/download-pdf', [SalesController::class, 'downloadInvoicePdf']);
});

// Keep the auth route but make it optional
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
