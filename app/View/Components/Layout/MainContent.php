<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainContent extends Component
{
    public function __construct(
        public string $title = '',
        public string $subtitle = '',
        public string $containerClass = 'px-4 sm:px-6 lg:px-8',
        public string $contentClass = '',
        public bool $showHeader = true,
        public bool $showBreadcrumbs = false,
        public array $breadcrumbs = [],
        public string $headerActions = ''
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.layout.main-content');
    }
}
