<?php

namespace Webkul\Asset\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static ?string $slug = 'assets/configurations';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Assets;
    }
}
