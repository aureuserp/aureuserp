<?php

namespace Webkul\Inventory\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Inventory\Filament\Clusters\PluginSettings;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageProducts as BaseManageProducts;

class ManageProducts extends BaseManageProducts
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-products';
}
