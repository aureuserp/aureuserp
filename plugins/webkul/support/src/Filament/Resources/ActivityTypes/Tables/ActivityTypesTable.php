<?php

namespace Webkul\Support\Filament\Resources\ActivityTypes\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityTypeAction;

class ActivityTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('support::filament/resources/activity-type.table.columns.name'))
                    ->sortable(),
                TextColumn::make('summary')
                    ->label(__('support::filament/resources/activity-type.table.columns.summary'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('delay_count')
                    ->label(__('support::filament/resources/activity-type.table.columns.planned-in'))
                    ->formatStateUsing(function ($record) {
                        return $record->delay_count ? "{$record->delay_count} {$record->delay_unit}" : 'No Delay';
                    }),
                TextColumn::make('delay_from')
                    ->label(__('support::filament/resources/activity-type.table.columns.type'))
                    ->formatStateUsing(fn ($state) => ActivityDelayFrom::options()[$state])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->label(__('support::filament/resources/activity-type.table.columns.action'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => ActivityTypeAction::options()[$state])
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.columns.status'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/activity-type.table.columns.created-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/activity-type.table.columns.updated-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('support::filament/resources/activity-type.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('category')
                    ->label(__('support::filament/resources/activity-type.table.groups.action-category'))
                    ->collapsible(),
                Tables\Grouping\Group::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('delay_count')
                    ->label(__('support::filament/resources/activity-type.table.groups.delay-count'))
                    ->collapsible(),
                Tables\Grouping\Group::make('delay_unit')
                    ->label(__('support::filament/resources/activity-type.table.groups.delay-unit'))
                    ->collapsible(),
                Tables\Grouping\Group::make('delay_from')
                    ->label(__('support::filament/resources/activity-type.table.groups.delay-source'))
                    ->collapsible(),
                Tables\Grouping\Group::make('chaining_type')
                    ->label(__('support::filament/resources/activity-type.table.groups.chaining-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('decoration_type')
                    ->label(__('support::filament/resources/activity-type.table.groups.decoration-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('defaultUser.name')
                    ->label(__('support::filament/resources/activity-type.table.groups.default-user'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('support::filament/resources/activity-type.table.groups.creation-date'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('support::filament/resources/activity-type.table.groups.last-update'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->multiple()
                    ->label(__('support::filament/resources/activity-type.table.filters.action'))
                    ->options(ActivityTypeAction::options()),
                TernaryFilter::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.filters.status')),
                Filter::make('has_delay')
                    ->label(__('support::filament/resources/activity-type.table.filters.has-delay'))
                    ->query(fn ($query) => $query->whereNotNull('delay_count')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.restore.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.force-delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('sort');
    }
}
