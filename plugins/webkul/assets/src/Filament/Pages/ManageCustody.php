<?php

namespace Webkul\Asset\Filament\Pages;

use UnitEnum;
use Webkul\Asset\Filament\Clusters\PluginSettings;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageCustody as BaseManageCustody;

class ManageCustody extends BaseManageCustody
{
    protected static ?string $cluster = PluginSettings::class;

    protected static ?string $slug = 'manage-custody';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return null;
    }
}
