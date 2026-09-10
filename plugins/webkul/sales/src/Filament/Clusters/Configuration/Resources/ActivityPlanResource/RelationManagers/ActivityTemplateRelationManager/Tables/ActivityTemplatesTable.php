<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Webkul\Support\Models\ActivityType;

class ActivityTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activityType.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.activity-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('summary')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.summary'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('responsible_type')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.assignment'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('responsible.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.assigned-to'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('delay_count')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.interval'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('delay_unit')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.delay-unit'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('delay_from')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.delay-from'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('activity_type_id')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.filters.activity-type'))
                    ->options(ActivityType::pluck('name', 'id')),
                TernaryFilter::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.filters.activity-status')),
                Filter::make('has_delay')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.filters.has-delay'))
                    ->query(fn ($query) => $query->whereNotNull('delay_count')),
            ])
            ->groups([
                Tables\Grouping\Group::make('responsible.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.groups.activity-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('responsible_type')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.groups.assignment'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.actions.create.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->modalWidth(Width::FitContent)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.actions.edit.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.actions.delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.bulk-actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->reorderable('sort');
    }
}
