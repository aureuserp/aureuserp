<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\TaskStagesRelationManager;
use Webkul\Project\Models\TaskStage;

class TaskStagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.columns.project'))
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.filters.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Group::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.groups.project')),
                Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort', direction: 'desc')
            ->defaultSort('sort', 'desc')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.restore.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (TaskStage $record) {
                        try {
                            $record->forceDelete();
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.title'))
                                    ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.title'))
                                    ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
