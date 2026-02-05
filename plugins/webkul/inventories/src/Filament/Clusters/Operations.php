<?php

namespace Webkul\Inventory\Filament\Clusters;

use Filament\Clusters\Cluster;

class Operations extends Cluster
{
    protected static ?string $slug = 'inventory/operations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations.navigation.group');
    }
}
