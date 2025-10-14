<?php

namespace Webkul\TimeOff\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;

class MyTime extends Cluster
{
    protected static ?int $navigationSort = 1;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'time-off/dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('time-off::filament/clusters/my-time.navigation.group');
    }
}
