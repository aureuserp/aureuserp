<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Filament\Actions\EditAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages\ViewActivityPlan as BaseViewActivityPlan;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewActivityPlan extends BaseViewActivityPlan
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof EditAction,
        ));
    }
}
