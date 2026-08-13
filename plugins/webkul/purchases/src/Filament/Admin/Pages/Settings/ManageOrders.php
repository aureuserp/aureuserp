<?php

namespace Webkul\Purchase\Filament\Admin\Pages\Settings;

use UnitEnum;
use Webkul\Purchase\Filament\Admin\Clusters\PluginSettings;
use Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageOrders as BaseManageOrders;

class ManageOrders extends BaseManageOrders
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-orders';
}
