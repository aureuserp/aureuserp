<?php

namespace Webkul\Support\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?int $navigationSort = 1000;

    public static function canAccessClusteredComponents(): bool
    {
        foreach (static::getClusteredComponents() as $component) {
            if ($component::shouldRegisterNavigation() && $component::canAccess()) {
                return true;
            }
        }

        return false;
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/clusters/settings/pages/settings.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/clusters/settings/pages/settings.navigation.group');
    }
}
