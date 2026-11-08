<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Tables;

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
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetProductExporter;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetProduct;

class ProductsTable
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
                    ->label(__('assets::filament/resources/product.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('type')
                    ->label(__('assets::filament/resources/product.table.columns.type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('category.full_name')
                    ->label(__('assets::filament/resources/product.table.columns.category'))
                    ->badge()
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->label(__('assets::filament/resources/product.table.columns.model'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cost')
                    ->label(__('assets::filament/resources/product.table.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('assets_count')
                    ->label(__('assets::filament/resources/product.table.columns.assets-count'))
                    ->sum('assets as assets_count', 'initial_quantity')
                    ->numeric(decimalPlaces: 0)
                    ->default(0)
                    ->sortable()
                    ->badge(),

                TextColumn::make('available_assets_count')
                    ->label(__('assets::filament/resources/product.table.columns.available-assets-count'))
                    ->sum(['assets as available_assets_count' => fn (Builder $query) => $query->where('status', AssetStatus::Available)], 'quantity')
                    ->numeric(decimalPlaces: 0)
                    ->default(0)
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('held_assets_count')
                    ->label(__('assets::filament/resources/product.table.columns.held-assets-count'))
                    ->sum(['assets as held_assets_count' => fn (Builder $query) => $query->where('status', AssetStatus::Held)], 'quantity')
                    ->numeric(decimalPlaces: 0)
                    ->default(0)
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('terminated_assets_count')
                    ->label(__('assets::filament/resources/product.table.columns.terminated-assets-count'))
                    ->counts(['assets as terminated_assets_count' => fn (Builder $query) => $query->whereIn('status', [AssetStatus::Scrapped, AssetStatus::Lost])])
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->toggleable(),

                TextColumn::make('assets_sum_cost')
                    ->label(__('assets::filament/resources/product.table.columns.total-cost'))
                    ->sum('assets', 'cost')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('0.00')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('assets::filament/resources/product.table.columns.is-active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('assets::filament/resources/product.table.columns.company'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label(__('assets::filament/resources/product.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('assets::filament/resources/product.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('assets::filament/resources/product.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('category.full_name')
                    ->label(__('assets::filament/resources/product.table.groups.category')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('assets::filament/resources/product.table.filters.type'))
                    ->options(AssetProductType::class),

                SelectFilter::make('category_id')
                    ->label(__('assets::filament/resources/product.table.filters.category'))
                    ->relationship('category', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? AssetCategory::applySubtreeFilter($query, $data['values'] ?? $data['value'], 'category_id')
                        : $query),

                TernaryFilter::make('is_active')
                    ->label(__('assets::filament/resources/product.table.filters.is-active')),

                SelectFilter::make('company_id')
                    ->label(__('assets::filament/resources/product.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->hidden(fn (AssetProduct $record): bool => $record->trashed()),
                EditAction::make()->hidden(fn (AssetProduct $record): bool => $record->trashed()),
                ActionGroup::make([
                    RestoreAction::make(),
                    DeleteAction::make()
                        ->using(function (AssetProduct $record, DeleteAction $action): void {
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
                        ->using(function (AssetProduct $record, ForceDeleteAction $action): void {
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
                    AssetProductExporter::configureExportAction(ExportBulkAction::make()
                        ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                        ->icon('heroicon-o-arrow-up-tray')
                        ->exporter(AssetProductExporter::class)
                        ->columnMappingColumns(2)
                        ->modalWidth('2xl')
                        ->chunkSize(250)
                        ->visible(fn (): bool => ProductResource::canViewAny())
                        ->authorize(fn (): bool => ProductResource::canViewAny())),
                ]),
                AssetProductExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetProductExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->visible(fn (): bool => ProductResource::canViewAny())
                    ->authorize(fn (): bool => ProductResource::canViewAny())),
            ]);
    }
}
