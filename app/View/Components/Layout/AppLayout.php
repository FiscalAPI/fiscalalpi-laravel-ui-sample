<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public function __construct(
        public string $title = 'Dashboard'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.layout.app-layout');
    }
}
