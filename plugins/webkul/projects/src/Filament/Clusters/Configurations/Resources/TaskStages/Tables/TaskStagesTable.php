<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStages\Tables;

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
use Webkul\Project\Filament\Resources\Projects\RelationManagers\TaskStagesRelationManager;

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
            ->reorderable('sort')
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
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.body')),
                    ),
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
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
