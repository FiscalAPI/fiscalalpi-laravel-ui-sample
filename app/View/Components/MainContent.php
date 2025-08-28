<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainContent extends Component
{
    public function __construct(
        public string $title = '',
        public string $breadcrumb = ''
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.main-content');
    }
}
