<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Orders extends Cluster
{
    protected static ?string $slug = 'sale/orders';

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
