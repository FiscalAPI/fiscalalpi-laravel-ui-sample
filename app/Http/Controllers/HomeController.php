<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // La raíz retorna directamente el dashboard
        return view('dashboard');
    }

    public function profile()
    {
        return view('profile');
    }
}
