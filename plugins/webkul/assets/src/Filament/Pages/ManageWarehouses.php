<?php

namespace Webkul\Asset\Filament\Pages;

use UnitEnum;
use Webkul\Asset\Filament\Clusters\PluginSettings;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageWarehouses as BaseManageWarehouses;

class ManageWarehouses extends BaseManageWarehouses
{
    protected static ?string $cluster = PluginSettings::class;

    protected static ?string $slug = 'manage-warehouses';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return null;
    }
}
