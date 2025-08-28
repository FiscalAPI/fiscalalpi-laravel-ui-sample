<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopNavigation extends Component
{
    public function __construct(
        public string $title = '',
        public array $navigationItems = []
    ) {
        $this->navigationItems = $navigationItems ?: $this->getDefaultNavigationItems();
    }

    private function getDefaultNavigationItems(): array
    {
        return [
            [
                'label' => 'Configuración',
                'route' => 'settings',
                'type' => 'link'
            ],
            [
                'label' => 'Cerrar Sesión',
                'route' => 'logout',
                'type' => 'button'
            ]
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.top-navigation');
    }
}
