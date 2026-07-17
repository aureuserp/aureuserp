<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'accounting/settings';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('accounting::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Accounting;
    }
}
