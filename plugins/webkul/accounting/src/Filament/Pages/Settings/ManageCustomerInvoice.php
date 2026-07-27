<?php

namespace Webkul\Accounting\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Accounting\Filament\Clusters\PluginSettings;
use Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageCustomerInvoice as BaseManageCustomerInvoice;

class ManageCustomerInvoice extends BaseManageCustomerInvoice
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-customer-invoice';
}
