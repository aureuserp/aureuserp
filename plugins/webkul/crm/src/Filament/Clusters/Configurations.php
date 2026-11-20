<?php

namespace Webkul\Crm\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static ?string $slug = 'crm/configurations';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations.navigation.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Crm;
    }
}
