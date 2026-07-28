<?php

namespace Webkul\Project\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Project\Filament\Clusters\PluginSettings;
use Webkul\Project\Filament\Clusters\Settings\Pages\ManageTime as BaseManageTime;

class ManageTime extends BaseManageTime
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-time';
}
