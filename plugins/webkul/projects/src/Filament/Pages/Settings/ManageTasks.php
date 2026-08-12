<?php

namespace Webkul\Project\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Project\Filament\Clusters\PluginSettings;
use Webkul\Project\Filament\Clusters\Settings\Pages\ManageTasks as BaseManageTasks;

class ManageTasks extends BaseManageTasks
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-tasks';
}
