<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileDropdown extends Component
{
    public function __construct(
        public string $userName = '',
        public string $userAvatar = '',
        public array $menuItems = []
    ) {
        $this->userName = $userName ?: config('layout.profile.default_user_name', 'Tom Cook');
        $this->userAvatar = $userAvatar ?: config('layout.profile.default_user_avatar', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80');
        $this->menuItems = $menuItems ?: config('layout.profile.menu_items', $this->getDefaultMenuItems());
    }

    public function render(): View|Closure|string
    {
        return view('components.layout.profile-dropdown');
    }

    private function getDefaultMenuItems(): array
    {
        return [
            [
                'label' => 'Your profile',
                'href' => '#',
                'icon' => null
            ],
            [
                'label' => 'Sign out',
                'href' => '#',
                'icon' => null
            ]
        ];
    }
}
