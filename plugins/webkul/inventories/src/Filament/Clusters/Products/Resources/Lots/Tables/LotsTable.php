<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Models\Lot;

class LotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.reference'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_quantity')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.on-hand-qty'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.groups.product')),
                Tables\Grouping\Group::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.groups.location')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.location'))
                    ->relationship('location', 'full_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->action(function (Lot $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
                        ->label(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.products.lots.actions.print', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Lot-Barcode.pdf');
                        }),
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ]);
    }
}
