<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\ActivityPlanResource;

class ViewActivityPlan extends ViewRecord
{
    protected static string $resource = ActivityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
