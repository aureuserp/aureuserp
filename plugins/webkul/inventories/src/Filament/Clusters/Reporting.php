<?php

namespace Webkul\Inventory\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Reporting extends Cluster
{
    protected static ?string $slug = 'inventory/reporting';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Inventory;
    }
}
