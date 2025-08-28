<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarItem extends Component
{
    public function __construct(
        public string $label,
        public string $route,
        public string $icon = 'default',
        public bool $active = false
    ) {
        $this->active = request()->routeIs($route);
    }

    public function render(): View|Closure|string
    {
        return view('components.sidebar-item');
    }
}
