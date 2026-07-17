<?php

namespace Webkul\Contact\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static ?string $slug = 'contact/configurations';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Contact;
    }
}
