<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Rules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Models\Rule;

class RulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.action'))
                    ->searchable(),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.source-location'))
                    ->searchable(),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.destination-location'))
                    ->searchable(),
                TextColumn::make('route.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.route'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.name'))
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.action'))
                    ->collapsible(),
                Tables\Grouping\Group::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.source-location'))
                    ->collapsible(),
                Tables\Grouping\Group::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.destination-location'))
                    ->collapsible(),
                Tables\Grouping\Group::make('route.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.route'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.action'))
                    ->options(RuleAction::class),
                SelectFilter::make('source_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.source-location'))
                    ->relationship('sourceLocation', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('destination_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.destination-location'))
                    ->relationship('destinationLocation', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('route_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.route'))
                    ->relationship('route', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Rule $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
