<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Tables;

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
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Models\BillOfMaterial;

class BillsOfMaterialTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns([
                TextColumn::make('code')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.quantity'))
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                TextColumn::make('uom.name')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.uom'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.type'))
                    ->badge(),
                TextColumn::make('company.name')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.company'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('type')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.filters.type'))
                    ->options(BillOfMaterialType::options()),
                SelectFilter::make('company_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (BillOfMaterial $record): bool => $record->trashed()),
                EditAction::make()
                    ->modalWidth(Width::SevenExtraLarge)
                    ->hidden(fn (BillOfMaterial $record): bool => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.restore.notification.title'))
                            ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.delete.notification.title'))
                            ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (BillOfMaterial $record, ForceDeleteAction $action): void {
                        try {
                            $record->forceDelete();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.force-delete.notification.error.title'))
                                ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.force-delete.notification.error.body'))
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.force-delete.notification.success.title'))
                            ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.restore.notification.title'))
                                ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.delete.notification.title'))
                                ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                $records->each(fn (Model $record): ?bool => $record->forceDelete());
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ]);
    }
}
