<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarNavigation extends Component
{
    public function __construct(
        public array $items = []
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.layout.sidebar-navigation');
    }
}
