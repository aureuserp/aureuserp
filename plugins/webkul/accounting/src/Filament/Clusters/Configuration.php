<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class Configuration extends Cluster
{
    protected static ?string $slug = 'accounting/configurations';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/configurations.navigation.group');
    }
}
