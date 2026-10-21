<?php

namespace Webkul\PointOfSale\Filament\Admin\Pages\Settings;

use UnitEnum;
use Webkul\PointOfSale\Filament\Admin\Clusters\PluginSettings;
use Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages\ManageAccounts as BaseManageAccounts;

class ManageAccounts extends BaseManageAccounts
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-accounts';
}
