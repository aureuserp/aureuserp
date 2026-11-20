<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages\ListActivityPlans as BaseListActivityPlans;

class ListActivityPlans extends BaseListActivityPlans
{
    protected static string $resource = ActivityPlanResource::class;

    protected static ?string $pluginName = 'crm';
}
