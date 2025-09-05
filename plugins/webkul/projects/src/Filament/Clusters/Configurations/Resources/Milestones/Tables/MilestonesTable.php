<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\Milestones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Project\Filament\Resources\Projects\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\Projects\RelationManagers\MilestonesRelationManager;

class MilestonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('deadline')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.deadline'))
                    ->dateTime()
                    ->sortable(),
                ToggleColumn::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.is-completed'))
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->completed_at = $state ? now() : null;
                    })
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.completed-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.project'))
                    ->hiddenOn([
                        MilestonesRelationManager::class,
                        ManageMilestones::class,
                    ])
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.creator'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.groups.project')),
                Group::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.groups.is-completed')),
                Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                TernaryFilter::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.filters.is-completed')),
                SelectFilter::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.filters.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn([
                        MilestonesRelationManager::class,
                        ManageMilestones::class,
                    ])
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/milestone.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/milestone.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/milestone.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/milestone.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/milestone.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/milestone.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
