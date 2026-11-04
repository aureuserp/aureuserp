<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Pages\Enums\SubNavigationPosition;

trait HasCRMClusterNavigation
{
    public function getSubNavigation(): array
    {
        if (blank($cluster = static::getCluster())) {
            return [];
        }

        return $this->generateNavigationItems($cluster::getClusteredComponents());
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }
}
