<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\EditActivityType as BaseEditActivityType;

class EditActivityType extends BaseEditActivityType
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'crm';

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
