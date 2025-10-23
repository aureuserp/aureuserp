<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class Accounting extends Cluster
{
    protected static ?string $slug = 'accounting/accounting';

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/accounting.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/accounting.navigation.group');
    }
}
