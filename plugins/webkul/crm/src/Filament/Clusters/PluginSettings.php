<?php

namespace Webkul\Crm\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'crm/settings';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('crm::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Crm;
    }
}
