<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource;
use Webkul\Project\Models\TaskStage;

class ManageTaskStages extends ManageRecords
{
    protected static string $resource = TaskStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('projects::filament/clusters/configurations/resources/task-stage/pages/manage-task-stages.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Task stage created')
                        ->body('The task stage has been created successfully.')
                        ->title(__('projects::filament/clusters/configurations/resources/task-stage/pages/manage-task-stages.header-actions.create.notification.title'))
                        ->body(__('projects::filament/clusters/configurations/resources/task-stage/pages/manage-task-stages.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('projects::filament/clusters/configurations/resources/task-stage/pages/manage-task-stages.tabs.all'))
                ->badge(TaskStage::count()),
            'archived' => Tab::make(__('projects::filament/clusters/configurations/resources/task-stage/pages/manage-task-stages.tabs.archived'))
                ->badge(TaskStage::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
