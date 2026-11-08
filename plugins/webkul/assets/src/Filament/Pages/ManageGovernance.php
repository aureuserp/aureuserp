<?php

namespace Webkul\Asset\Filament\Pages;

use UnitEnum;
use Webkul\Asset\Filament\Clusters\PluginSettings;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageGovernance as BaseManageGovernance;

class ManageGovernance extends BaseManageGovernance
{
    protected static ?string $cluster = PluginSettings::class;

    protected static ?string $slug = 'manage-governance';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return null;
    }
}
