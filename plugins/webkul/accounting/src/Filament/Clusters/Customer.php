<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class Customer extends Cluster
{
    protected static ?string $slug = 'accounting/customers';

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/customers.navigation.group');
    }
}
