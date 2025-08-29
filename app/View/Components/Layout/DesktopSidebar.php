<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DesktopSidebar extends Component
{
    public function __construct(
        public array $navigationItems = []
    ) {
        $this->navigationItems = $navigationItems ?: $this->getDefaultNavigationItems();
    }

    public function render(): View|Closure|string
    {
        return view('components.layout.desktop-sidebar');
    }

    private function getDefaultNavigationItems(): array
    {
        return [
            [
                'name' => 'Inicio',
                'href' => '/',
                'active' => true,
                'icon' => 'dashboard'
            ],
            [
                'name' => 'Empresas',
                'href' => '/users',
                'active' => false,
                'icon' => 'building-office2'
            ],
        ];
    }
}
