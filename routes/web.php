<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


// La ruta raíz retorna directamente el dashboard
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile', [HomeController::class, 'profile'])->name('profile');

// Rutas adicionales para el ejemplo
Route::get('/settings', function () {
    return view('settings');
})->name('settings');

Route::post('/logout', function () {
    return redirect('/');
})->name('logout');
