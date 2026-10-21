<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Reporting extends Cluster
{
    protected static ?string $slug = 'point-of-sale/reporting';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::PointOfSale;
    }
}
