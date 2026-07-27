<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configuration extends Cluster
{
    protected static ?string $slug = 'accounting/configurations';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations.navigation.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('accounting::filament/clusters/configurations.navigation.title');
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Accounting;
    }
}
