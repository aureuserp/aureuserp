<?php

namespace Webkul\Purchase\Filament\Admin\Pages\Settings;

use UnitEnum;
use Webkul\Purchase\Filament\Admin\Clusters\PluginSettings;
use Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageProducts as BaseManageProducts;

class ManageProducts extends BaseManageProducts
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-products';
}
