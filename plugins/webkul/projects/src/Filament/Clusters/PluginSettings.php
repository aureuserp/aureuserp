<?php

namespace Webkul\Project\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'project/settings';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('projects::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Project;
    }
}
