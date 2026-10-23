<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Pages\Enums\SubNavigationPosition;

trait HasCrmClusterNavigation
{
    public function getSubNavigation(): array
    {
        if (blank($cluster = static::getCluster())) {
            return [];
        }

        $navigation = $this->generateNavigationItems($cluster::getClusteredComponents());

        return count($navigation) > 1 ? $navigation : [];
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }
}
