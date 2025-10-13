<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;

class Configuration extends Cluster
{
    protected static ?string $slug = 'sale/configurations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/configurations.navigation.group');
    }
}
