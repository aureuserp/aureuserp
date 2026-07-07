<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Vendors extends Cluster
{
    protected static ?string $slug = 'invoices/vendors';

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Invoice;
    }
}
