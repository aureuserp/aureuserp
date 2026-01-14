<?php

namespace Webkul\Purchase\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Orders extends Cluster
{
    protected static ?string $slug = 'purchase/orders';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('purchases::filament/admin/clusters/orders.navigation.group');
    }
}
