<?php

namespace Webkul\Website\Filament\Customer\Clusters;

use Filament\Clusters\Cluster;
use Filament\Facades\Filament;

class Account extends Cluster
{
    protected static ?int $navigationSort = 1000;

    public static function getNavigationLabel(): string
    {
        return __('website::filament/customer/clusters/account.navigation.title');
    }

    // public static function canAccess(): bool
    // {
    //     return false;
    //     return Filament::auth()->check();
    // }

    public static function canAccessClusteredComponents(): bool
    {
        return false;

        return Filament::auth()->check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
