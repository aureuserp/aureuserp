<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewActivityPlan extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/configurations/resources/activity-plan/pages/view-activity-plan.header-actions.delete.notification.title'))
                        ->body(__('sales::filament/clusters/configurations/resources/activity-plan/pages/view-activity-plan.header-actions.delete.notification.body')),
                ),
        ];
    }
}
