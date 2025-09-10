<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\ListActivityPlans as BaseListActivityPlans;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\ActivityPlanResource;

class ListActivityPlans extends BaseListActivityPlans
{
    protected static string $resource = ActivityPlanResource::class;

    protected static ?string $pluginName = 'recruitments';
}
