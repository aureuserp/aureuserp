<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;

class Vendors extends Cluster
{
    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('invoices::filament/clusters/vendors.navigation.group');
    }
}
