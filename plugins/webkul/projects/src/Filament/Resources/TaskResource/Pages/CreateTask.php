<?php

namespace Webkul\Project\Filament\Resources\TaskResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Project;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function afterFill(): void
    {
        if (! ($projectId = request()->integer('project_id')) || ! ($project = Project::find($projectId))) {
            return;
        }

        $this->data['project_id'] = $project->id;
        $this->data['partner_id'] = $project->partner_id;
        $this->data['stage_id'] = TaskResource::getDefaultStageId($project->id);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('projects::filament/resources/task/pages/create-task.notification.title'))
            ->body(__('projects::filament/resources/task/pages/create-task.notification.body'));
    }
}
