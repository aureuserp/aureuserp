<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Models\Category;

class QuantitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->groups([
                Group::make('product.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.product')),
                Group::make('product.category.full_name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.product-category')),
                Group::make('location.full_name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.location')),
                Group::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.storage-category')),
                Group::make('lot.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.lot')),
                Group::make('package.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.package')),
                Group::make('company.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.company')),
            ])
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.location'))
                    ->searchable()
                    ->sortable()
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.storage-category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(QuantityResource::getOperationSettings()->enable_packages),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.lot'))
                    ->searchable()
                    ->placeholder('—')
                    ->visible(QuantityResource::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextInputColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand'))
                    ->sortable()
                    ->rules(['numeric', 'min:1', 'max:999999999'])
                    ->beforeStateUpdated(function ($record, $state) {
                        $previousQuantity = $record->quantity;

                        if ($previousQuantity == $state) {
                            return;
                        }

                        $record->update([
                            'quantity'                => $state,
                            'inventory_diff_quantity' => $state - $previousQuantity,
                        ]);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand-before-state-updated.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand-before-state-updated.notification.body'))
                            ->send();
                    })
                    ->summarize(Sum::make()),
                TextColumn::make('reserved_quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.reserved-quantity'))
                    ->sortable()
                    ->summarize(Sum::make()),
                TextColumn::make('product.uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.unit'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(QuantityResource::getProductSettings()->enable_uom),
            ])
            ->filters([
                SelectFilter::make('warehouse')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.warehouse'))
                    ->options(fn () => Warehouse::query()->orderBy('name')->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations)
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('location', fn (Builder $q) => $q->whereIn('warehouse_id', $data['values']))),
                SelectFilter::make('location')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.location'))
                    ->relationship('location', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                SelectFilter::make('product_category')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.product-category'))
                    ->options(fn () => Category::get()->pluck('full_name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('product', fn (Builder $q) => $q->whereIn('category_id', $data['values']))),
                SelectFilter::make('storageCategory')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.storage-category'))
                    ->relationship('storageCategory', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                SelectFilter::make('package')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.package'))
                    ->relationship('package', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(QuantityResource::getOperationSettings()->enable_packages),
                SelectFilter::make('package_type')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.package-type'))
                    ->options(fn () => PackageType::query()->orderBy('name')->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(QuantityResource::getOperationSettings()->enable_packages)
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('package', fn (Builder $q) => $q->whereIn('package_type_id', $data['values']))),
                SelectFilter::make('lot')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.lot'))
                    ->relationship('lot', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(QuantityResource::getTraceabilitySettings()->enable_lots_serial_numbers),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['location_id'] = $data['location_id'] ?? Warehouse::query()->when(Product::find($data['product_id'])?->company_id, fn ($query, $scopedCompanyId) => $query->where(owned_by_company($scopedCompanyId)))->value('lot_stock_location_id');

                        $data['company_id'] = Product::find($data['product_id'])?->company_id;

                        $data['inventory_diff_quantity'] = $data['quantity'];

                        return $data;
                    })
                    ->before(function (array $data) {
                        $existingQuantity = ProductQuantity::where('location_id', $data['location_id'] ?? Warehouse::query()->when(Product::find($data['product_id'])?->company_id, fn ($query, $scopedCompanyId) => $query->where(owned_by_company($scopedCompanyId)))->value('lot_stock_location_id'))
                            ->where('product_id', $data['product_id'])
                            ->where('package_id', $data['package_id'] ?? null)
                            ->where('lot_id', $data['lot_id'] ?? null)
                            ->exists();

                        if ($existingQuantity) {
                            Notification::make()
                                ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.before.notification.title'))
                                ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.before.notification.body'))
                                ->warning()
                                ->send();

                            $this->halt();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.actions.delete.notification.body')),
                    ),
            ]);
    }
}
