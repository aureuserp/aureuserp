<?php

namespace Webkul\TimeOff\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;

class Management extends Cluster
{
    protected static ?int $navigationSort = 3;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'time-off/management';
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/management.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('time-off::filament/clusters/management.navigation.group');
    }
}
