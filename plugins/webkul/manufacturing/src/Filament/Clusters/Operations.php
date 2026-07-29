<?php

namespace Webkul\Manufacturing\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Operations extends Cluster
{
    protected static ?string $slug = 'manufacturing/operations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Manufacturing;
    }
}
