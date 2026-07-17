<?php

namespace Webkul\Inventory\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'inventory/settings';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('inventories::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Inventory;
    }
}
