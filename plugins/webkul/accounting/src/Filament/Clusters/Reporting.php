<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class Reporting extends Cluster
{
    protected static ?string $slug = 'accounting/reporting';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/reporting.navigation.group');
    }
}
