<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Filament\Actions\ViewAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages\EditActivityPlan as BaseEditActivityPlan;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditActivityPlan extends BaseEditActivityPlan
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof ViewAction,
        ));
    }
}
