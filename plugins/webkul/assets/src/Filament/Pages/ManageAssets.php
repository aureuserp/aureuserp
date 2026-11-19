<?php

namespace Webkul\Asset\Filament\Pages;

use UnitEnum;
use Webkul\Asset\Filament\Clusters\PluginSettings;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageAssets as BaseManageAssets;

class ManageAssets extends BaseManageAssets
{
    protected static ?string $cluster = PluginSettings::class;

    protected static ?string $slug = 'manage-assets';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return null;
    }
}
