<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Tables;

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
use Webkul\Inventory\Enums;
use Webkul\Inventory\Models\OperationType;

class OperationTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.name'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.company'))
                    ->searchable(),
                TextColumn::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.warehouse'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.warehouse'))
                    ->collapsible(),
                Tables\Grouping\Group::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.type'))
                    ->options(Enums\OperationType::class)
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('warehouse_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.warehouse'))
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (OperationType $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/operation-type.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.table.empty-actions.create.label'))
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
