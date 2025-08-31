<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share navigation items with all views
        view()->composer('components.layout.sidebar-navigation', function ($view) {
            $view->with('items', [
                [
                    'name' => 'Dashboard',
                    'href' => '/',
                    'icon' => 'dashboard',
                    'active' => request()->is('/')
                ],
                [
                    'name' => 'Productos',
                    'href' => '/products',
                    'icon' => 'box',
                    'active' => request()->is('products*')
                ],
                [
                    'name' => 'Personas',
                    'href' => '/people',
                    'icon' => 'team',
                    'active' => request()->is('people*')
                ],
                [
                    'name' => 'Punto de Venta',
                    'href' => '/pos',
                    'icon' => 'pos',
                    'active' => request()->is('pos*')
                ],
            ]);
        });
    }
}
