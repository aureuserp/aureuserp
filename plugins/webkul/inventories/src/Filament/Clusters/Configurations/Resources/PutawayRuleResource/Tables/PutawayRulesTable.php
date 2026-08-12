<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Models\PutawayRule;

class PutawayRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.in-location'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.product'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.category'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.storage-category'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('outLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.out-location'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sub_location')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.sub-location'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.company'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (PutawayRule $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
