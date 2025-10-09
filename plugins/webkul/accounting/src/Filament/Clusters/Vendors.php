<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class Vendors extends Cluster
{
    protected static ?string $slug = 'accounting/vendors';

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/vendors.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/vendors.navigation.group');
    }
}
