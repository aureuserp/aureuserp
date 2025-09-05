<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\ViewActivityPlan as BaseViewActivityPlan;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\ActivityPlanResource;

class ViewActivityPlan extends BaseViewActivityPlan
{
    protected static string $resource = ActivityPlanResource::class;
}
