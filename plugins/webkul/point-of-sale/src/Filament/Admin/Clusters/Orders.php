<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Orders extends Cluster
{
    protected static ?string $slug = 'point-of-sale/orders';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::PointOfSale;
    }
}
