<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;

class ToInvoice extends Cluster
{
    protected static ?string $slug = 'sale/invoice';

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/to-invoice.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/to-invoice.navigation.group');
    }
}
