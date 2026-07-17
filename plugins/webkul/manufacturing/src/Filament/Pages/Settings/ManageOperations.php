<?php

namespace Webkul\Manufacturing\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Manufacturing\Filament\Clusters\PluginSettings;
use Webkul\Manufacturing\Filament\Clusters\Settings\Pages\ManageOperations as BaseManageOperations;

class ManageOperations extends BaseManageOperations
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-operations';
}
