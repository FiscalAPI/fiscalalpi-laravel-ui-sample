<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Topbar extends Component
{
    public function __construct(
        public string $searchPlaceholder = '',
        public bool $showSearch = true,
        public bool $showNotifications = true,
        public string $searchAction = '',
        public string $searchMethod = ''
    ) {
        // Solo aplicar configuración por defecto si no se proporcionan valores explícitos
        if ($searchPlaceholder === '') {
            $this->searchPlaceholder = config('layout.topbar.default_search_placeholder', 'Search');
        }

        if ($searchAction === '') {
            $this->searchAction = config('layout.topbar.search_action', '#');
        }

        if ($searchMethod === '') {
            $this->searchMethod = config('layout.topbar.search_method', 'GET');
        }

        // Los booleanos ya tienen valores por defecto, no necesitan configuración
        $this->showSearch = $showSearch;
        $this->showNotifications = $showNotifications;
    }

    public function render(): View|Closure|string
    {
        return view('components.layout.topbar');
    }
}
