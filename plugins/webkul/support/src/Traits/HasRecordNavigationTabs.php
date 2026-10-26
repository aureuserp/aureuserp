<?php

namespace Webkul\Support\Traits;

use Filament\Pages\Enums\SubNavigationPosition;

trait HasRecordNavigationTabs
{
    public function getRecordNavigationTabs(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }
}
