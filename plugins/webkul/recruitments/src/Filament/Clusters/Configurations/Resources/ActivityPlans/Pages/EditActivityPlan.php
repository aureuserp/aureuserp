<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\EditActivityPlan as BaseEditActivityPlan;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\ActivityPlanResource;

class EditActivityPlan extends BaseEditActivityPlan
{
    protected static string $resource = ActivityPlanResource::class;
}
