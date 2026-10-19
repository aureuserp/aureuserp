<?php

namespace Webkul\Crm\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Sales extends Cluster
{
    protected static ?string $slug = 'crm/sales';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Crm;
    }
}
