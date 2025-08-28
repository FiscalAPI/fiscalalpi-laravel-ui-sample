<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopNavigationItem extends Component
{
    public function __construct(
        public string $label,
        public string $route,
        public string $type = 'link'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.top-navigation-item');
    }
}
