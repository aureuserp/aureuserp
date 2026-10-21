<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Products extends Cluster
{
    protected static ?string $slug = 'point-of-sale/products';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::PointOfSale;
    }
}
