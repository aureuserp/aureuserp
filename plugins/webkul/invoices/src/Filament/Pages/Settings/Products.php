<?php

namespace Webkul\Invoice\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Invoice\Filament\Clusters\PluginSettings;
use Webkul\Invoice\Filament\Clusters\Settings\Pages\Products as BaseProducts;

class Products extends BaseProducts
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'products';
}
