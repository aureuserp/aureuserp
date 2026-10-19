<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages\EditActivityPlan as BaseEditActivityPlan;

class EditActivityPlan extends BaseEditActivityPlan
{
    protected static string $resource = ActivityPlanResource::class;
}
