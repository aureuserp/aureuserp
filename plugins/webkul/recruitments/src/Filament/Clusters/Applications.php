<?php

namespace Webkul\Recruitment\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;

class Applications extends Cluster
{
    protected static ?int $navigationSort = 2;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'recruitments/applications';
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/applications.navigation.group');
    }
}
