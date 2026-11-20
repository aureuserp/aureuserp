<?php

namespace Webkul\Crm\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Crm\Filament\Clusters\PluginSettings;
use Webkul\Crm\Filament\Clusters\Settings\Pages\ManageLeadScoring as BaseManageLeadScoring;

class ManageLeadScoring extends BaseManageLeadScoring
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-lead-scoring';
}
