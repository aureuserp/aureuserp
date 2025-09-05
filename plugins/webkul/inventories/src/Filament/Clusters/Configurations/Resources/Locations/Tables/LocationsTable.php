<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Locations\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
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
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Pages\ManageLocations;
use Webkul\Inventory\Models\Location;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.location'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.type'))
                    ->searchable(),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.storage-category'))
                    ->placeholder('—')
                    ->sortable()
                    ->hiddenOn(ManageLocations::class),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.company'))
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('warehouse.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.warehouse'))
                    ->collapsible(),
                Tables\Grouping\Group::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.filters.type'))
                    ->options(LocationType::class)
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('storage_category_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.filters.location'))
                    ->relationship('storageCategory', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/location.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->modalWidth('6xl'),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->modalWidth('6xl')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Location $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/location.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
                        ->label(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.configurations.locations.actions.print', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Location-Barcode.pdf');
                        }),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/location.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
