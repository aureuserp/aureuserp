<?php

namespace Webkul\Sale\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Sale\Filament\Clusters\PluginSettings;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManageQuotationAndOrder as BaseManageQuotationAndOrder;

class ManageQuotationAndOrder extends BaseManageQuotationAndOrder
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-quotation-and-order';
}
