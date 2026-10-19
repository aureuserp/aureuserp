<?php

namespace Webkul\Crm\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Leads extends Cluster
{
    protected static ?string $slug = 'crm/leads';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/leads.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Crm;
    }
}
