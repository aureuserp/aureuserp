<?php

namespace Webkul\Project\Filament\Resources\TaskResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Support\Models\ActivityPlan;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource)
                ->setActivityPlans($this->getActivityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('projects::filament/resources/task/pages/view-task.header-actions.delete.notification.title'))
                        ->body(__('projects::filament/resources/task/pages/view-task.header-actions.delete.notification.body')),
                ),
        ];
    }

    private function getActivityPlans(): mixed
    {
        return ActivityPlan::where('plugin', 'projects')->pluck('name', 'id');
    }
}
