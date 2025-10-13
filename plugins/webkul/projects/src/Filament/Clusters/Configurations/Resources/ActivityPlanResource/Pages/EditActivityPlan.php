<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource;

class EditActivityPlan extends EditRecord
{
    protected static string $resource = ActivityPlanResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('projects::filament/clusters/configurations/resources/activity-plan/pages/edit-activity-plan.notification.title'))
            ->body(__('projects::filament/clusters/configurations/resources/activity-plan/pages/edit-activity-plan.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('projects::filament/clusters/configurations/resources/activity-plan/pages/edit-activity-plan.header-actions.delete.notification.title'))
                        ->body(__('projects::filament/clusters/configurations/resources/activity-plan/pages/edit-activity-plan.header-actions.delete.notification.body')),
                ),
        ];
    }
}
