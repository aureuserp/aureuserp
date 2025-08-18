<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;

class Orders extends Cluster
{
    protected static ?string $slug = 'sale/orders';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/orders.navigation.group');
    }
}
