<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;

class Customer extends Cluster
{
    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('invoices::filament/clusters/customers.navigation.group');
    }
}
