<?php

namespace Webkul\Accounting\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Accounting\Filament\Clusters\PluginSettings;
use Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageTaxes as BaseManageTaxes;

class ManageTaxes extends BaseManageTaxes
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-taxes';
}
