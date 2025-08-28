<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public function __construct(
        public array $menuItems = []
    ) {
        $this->menuItems = $menuItems ?: $this->getDefaultMenuItems();
    }

    private function getDefaultMenuItems(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'route' => 'home', // Apunta a la ruta raíz
                'icon' => 'dashboard'
            ],
            [
                'label' => 'Perfil',
                'route' => 'profile',
                'icon' => 'user'
            ]
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
