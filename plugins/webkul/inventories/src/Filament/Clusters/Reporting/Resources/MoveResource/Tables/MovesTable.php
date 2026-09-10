<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource;
use Webkul\Inventory\Models\PackageType;
use Webkul\Product\Models\Category;

class MovesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('product.name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.product')),
                Group::make('state')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.status')),
                Group::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.date'))
                    ->date(),
                Group::make('operation.name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.operation')),
                Group::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.location')),
                Group::make('product.category.full_name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.category')),
            ])
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.product'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.date'))
                    ->sortable()
                    ->dateTime(),
                TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.lot'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(MoveResource::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextColumn::make('resultPackage.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.package'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(MoveResource::getOperationSettings()->enable_packages),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.source-location'))
                    ->visible(MoveResource::getWarehouseSettings()->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.destination-location'))
                    ->visible(MoveResource::getWarehouseSettings()->enable_locations),
                TextColumn::make('uom_qty')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.quantity'))
                    ->sortable()
                    ->color(fn ($record) => $record->destinationLocation->type == LocationType::INTERNAL ? 'success' : 'danger'),
                TextColumn::make('uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.unit'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(MoveResource::getProductSettings()->enable_uom),
                TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.done-by'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('product_category')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.product-category'))
                    ->options(fn () => Category::get()->pluck('full_name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('product', fn (Builder $q) => $q->whereIn('category_id', $data['values']))),
                SelectFilter::make('sourceLocation')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.source-location'))
                    ->relationship('sourceLocation', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(MoveResource::getWarehouseSettings()->enable_locations),
                SelectFilter::make('destinationLocation')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.destination-location'))
                    ->relationship('destinationLocation', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(MoveResource::getWarehouseSettings()->enable_locations),
                SelectFilter::make('resultPackage')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.package'))
                    ->relationship('resultPackage', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(MoveResource::getOperationSettings()->enable_packages),
                SelectFilter::make('package_type')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.package-type'))
                    ->options(fn () => PackageType::query()->orderBy('name')->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(MoveResource::getOperationSettings()->enable_packages)
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('resultPackage', fn (Builder $q) => $q->whereIn('package_type_id', $data['values']))),
                SelectFilter::make('lot')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.lot'))
                    ->relationship('lot', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(MoveResource::getTraceabilitySettings()->enable_lots_serial_numbers),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.actions.delete.notification.body')),
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
