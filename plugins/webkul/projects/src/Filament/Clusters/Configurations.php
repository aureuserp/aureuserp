<?php

namespace Webkul\Project\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class Configurations extends Cluster
{
    protected static ?string $slug = 'project/configurations';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/clusters/configurations.navigation.group');
    }
}
