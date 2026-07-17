<?php

namespace Webkul\Sale\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Sale\Filament\Clusters\PluginSettings;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManageInvoice as BaseManageInvoice;

class ManageInvoice extends BaseManageInvoice
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-invoicing';
}
