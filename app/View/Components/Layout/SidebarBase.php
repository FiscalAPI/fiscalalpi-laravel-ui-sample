<?php

namespace App\View\Components\Layout;

use App\View\Components\Layout\Concerns\HasNavigation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarBase extends Component
{
    use HasNavigation;

    public function __construct(
        public array $navigationItems = []
    ) {
        $this->navigationItems = $navigationItems ?: $this->getNavigationItems();
    }

    public function render(): View|Closure|string
    {
        return view('components.layout.sidebar-base', [
            'branding' => $this->getBranding()
        ]);
    }
}
