<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetWarehouseExporter;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Models\AssetWarehouse;

class AssetWarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('name')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.code'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('custodian.name')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.custodian'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('assets_count')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.assets-count'))
                    ->counts(['assets' => fn (Builder $query) => $query->where('status', AssetStatus::Available)])
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('assets_sum_cost')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.total-cost'))
                    ->sum(['assets' => fn (Builder $query) => $query->where('status', AssetStatus::Available)], 'cost')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('0.00')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.is-active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.company'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.creator'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('assets::filament/resources/asset-warehouse.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('custodian_employee_id')
                    ->label(__('assets::filament/resources/asset-warehouse.table.filters.custodian'))
                    ->relationship('custodian', 'name')
                    ->searchable()
                    ->multiple(),

                TernaryFilter::make('is_active')
                    ->label(__('assets::filament/resources/asset-warehouse.table.filters.is-active')),

                SelectFilter::make('company_id')
                    ->label(__('assets::filament/resources/asset-warehouse.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->hidden(fn (AssetWarehouse $record): bool => $record->trashed()),
                EditAction::make()->hidden(fn (AssetWarehouse $record): bool => $record->trashed()),
                ActionGroup::make([
                    RestoreAction::make(),
                    DeleteAction::make()
                        ->using(function (AssetWarehouse $record, DeleteAction $action): void {
                            try {
                                $record->delete();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                    ForceDeleteAction::make()
                        ->using(function (AssetWarehouse $record, ForceDeleteAction $action): void {
                            try {
                                $record->forceDelete();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->using(function (Collection $records, DeleteBulkAction $action): void {
                            try {
                                DB::transaction(fn () => $records->each->delete());
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                    ForceDeleteBulkAction::make()
                        ->using(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                DB::transaction(fn () => $records->each->forceDelete());
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                    RestoreBulkAction::make(),
                    AssetWarehouseExporter::configureExportAction(ExportBulkAction::make()
                        ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                        ->icon('heroicon-o-arrow-up-tray')
                        ->exporter(AssetWarehouseExporter::class)
                        ->columnMappingColumns(2)
                        ->modalWidth('2xl')
                        ->chunkSize(250)
                        ->visible(fn (): bool => AssetWarehouseResource::canViewAny())
                        ->authorize(fn (): bool => AssetWarehouseResource::canViewAny())),
                ]),
                AssetWarehouseExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetWarehouseExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->visible(fn (): bool => AssetWarehouseResource::canViewAny())
                    ->authorize(fn (): bool => AssetWarehouseResource::canViewAny())),
            ]);
    }
}
