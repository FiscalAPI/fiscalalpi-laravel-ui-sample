<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileDropdown extends Component
{
    public function __construct(
        public string $userName = 'Tom Cook',
        public string $userAvatar = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.layout.profile-dropdown');
    }
}
