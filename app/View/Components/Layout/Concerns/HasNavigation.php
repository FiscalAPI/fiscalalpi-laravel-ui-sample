<?php

namespace App\View\Components\Layout\Concerns;

use App\Services\NavigationService;

trait HasNavigation
{
    protected ?NavigationService $navigationService = null;

    protected function getNavigationService(): NavigationService
    {
        if ($this->navigationService === null) {
            $this->navigationService = new NavigationService();
        }
        
        return $this->navigationService;
    }

    protected function getNavigationItems(): array
    {
        return $this->getNavigationService()->getNavigationItems();
    }

    protected function getBranding(): array
    {
        return $this->getNavigationService()->getBranding();
    }
}
