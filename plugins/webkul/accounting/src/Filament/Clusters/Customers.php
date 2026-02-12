<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class Customers extends Cluster
{
    protected static ?string $slug = 'accounting/customers';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/customers.navigation.group');
    }
}
