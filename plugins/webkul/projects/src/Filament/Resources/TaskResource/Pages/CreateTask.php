<?php

namespace Webkul\Project\Filament\Resources\TaskResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Project\Filament\Resources\TaskResource;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('projects::filament/resources/task/pages/create-task.notification.title'))
            ->body(__('projects::filament/resources/task/pages/create-task.notification.body'));
    }
}
