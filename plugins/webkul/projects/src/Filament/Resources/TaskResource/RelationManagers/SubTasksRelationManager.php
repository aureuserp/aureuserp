<?php

namespace Webkul\Project\Filament\Resources\TaskResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;

class SubTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'subTasks';

    public function form(Schema $schema): Schema
    {
        return TaskResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TaskResource::table($table)
            ->filters([
                TrashedFilter::make(),
            ])
            ->filtersLayout(FiltersLayout::Dropdown)
            ->filtersFormColumns(1)
            ->filtersTriggerAction(null)
            ->groups([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('projects::filament/resources/task/relation-managers/sub-tasks.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->fillForm(function (array $arguments): array {
                        return [
                            'stage_id'     => TaskStage::first()?->id,
                            'state'        => TaskState::IN_PROGRESS,
                            'project_id'   => $this->getOwnerRecord()->project_id,
                            'milestone_id' => $this->getOwnerRecord()->milestone_id,
                            'partner_id'   => $this->getOwnerRecord()->partner_id,
                            'users'        => $this->getOwnerRecord()->users->pluck('id')->toArray(),
                        ];
                    })
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->modalWidth('6xl')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task/relation-managers/sub-tasks.table.header-actions.create.notification.title'))
                            ->body(__('projects::filament/resources/task/relation-managers/sub-tasks.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Task $record): string => TaskResource::getUrl('view', ['record' => $record]))
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->url(fn (Task $record): string => TaskResource::getUrl('edit', ['record' => $record]))
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task/relation-managers/sub-tasks.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task/relation-managers/sub-tasks.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task/relation-managers/sub-tasks.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task/relation-managers/sub-tasks.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task/relation-managers/sub-tasks.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task/relation-managers/sub-tasks.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return TaskResource::infolist($schema);
    }
}
