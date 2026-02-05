<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;

class Products extends Cluster
{
    protected static ?string $slug = 'sale/products';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/products.navigation.group');
    }
}
