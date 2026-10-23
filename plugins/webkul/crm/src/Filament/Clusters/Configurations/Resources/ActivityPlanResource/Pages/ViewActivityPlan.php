<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages\ViewActivityPlan as BaseViewActivityPlan;

class ViewActivityPlan extends BaseViewActivityPlan
{
    protected static string $resource = ActivityPlanResource::class;
}
