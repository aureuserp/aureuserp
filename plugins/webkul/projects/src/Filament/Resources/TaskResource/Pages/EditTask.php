<?php

namespace Webkul\Project\Filament\Resources\TaskResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Support\Models\ActivityPlan;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('projects::filament/resources/task/pages/edit-task.notification.title'))
            ->body(__('projects::filament/resources/task/pages/edit-task.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getActivityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('projects::filament/resources/task/pages/edit-task.header-actions.delete.notification.title'))
                        ->body(__('projects::filament/resources/task/pages/edit-task.header-actions.delete.notification.body')),
                ),
        ];
    }

    private function getActivityPlans(): mixed
    {
        return ActivityPlan::projects()->pluck('name', 'id');
    }
}
