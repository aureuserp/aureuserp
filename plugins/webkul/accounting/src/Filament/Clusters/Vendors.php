<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Vendors extends Cluster
{
    protected static ?string $slug = 'accounting/vendors';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/vendors.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Accounting;
    }
}
