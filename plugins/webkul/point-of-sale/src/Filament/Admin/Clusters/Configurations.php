<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static ?string $slug = 'point-of-sale/configurations';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::PointOfSale;
    }
}
