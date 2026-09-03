<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;

class QuantitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.location'))
                    ->searchable()
                    ->sortable()
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.storage-category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(QuantityResource::getWarehouseSettings()->enable_locations),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.category.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.product-category'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.lot'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(QuantityResource::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(QuantityResource::getOperationSettings()->enable_packages),
                TextColumn::make('available_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.available-quantity'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.on-hand'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('product.uom.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.uom'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(QuantityResource::getProductSettings()->enable_uom),
                TextInputColumn::make('counted_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.counted'))
                    ->sortable()
                    ->rules(['numeric', 'min:0'])
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->update([
                            'inventory_quantity_set'  => true,
                            'inventory_diff_quantity' => $state - $record->quantity,
                        ]);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.columns.on-hand-before-state-updated.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.columns.on-hand-before-state-updated.notification.body'))
                            ->success()
                            ->send();
                    }),
                TextColumn::make('inventory_diff_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.difference'))
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->inventory_quantity_set ? $record->inventory_diff_quantity : '')
                    ->color(fn ($record) => $record->inventory_diff_quantity > 0 ? 'success' : 'danger'),
                TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.scheduled-at'))
                    ->sortable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('user.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.user'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.company'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(
                collect([
                    Group::make('product.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.product')),
                    Group::make('product.category.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.product-category')),
                    Group::make('location.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.location')),
                    Group::make('storageCategory.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.storage-category')),
                    Group::make('lot.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.lot')),
                    Group::make('package.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.package')),
                    Group::make('company.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.company')),
                ])->filter(function ($group) {
                    return match ($group->getId()) {
                        'location.full_name', 'storageCategory.name' => QuantityResource::getWarehouseSettings()->enable_locations,
                        'lot.name'                                   => QuantityResource::getTraceabilitySettings()->enable_lots_serial_numbers,
                        'package.name'                               => QuantityResource::getOperationSettings()->enable_packages,
                        default                                      => true
                    };
                })->all()
            )
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect([
                        QuantityResource::getWarehouseSettings()->enable_locations
                            ? RelationshipConstraint::make('location')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.location'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('full_name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-map-pin')
                            : null,
                        QuantityResource::getWarehouseSettings()->enable_locations
                            ? RelationshipConstraint::make('storageCategory')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.storage-category'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-folder')
                                : null,
                        RelationshipConstraint::make('product')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.product'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-shopping-bag'),
                        QuantityResource::getProductSettings()->enable_uom
                            ? RelationshipConstraint::make('uom')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.uom'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-shopping-bag')
                            : null,
                        RelationshipConstraint::make('product.category')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.product-category'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->modifyQueryUsing(fn ($query) => $query->select('categories.*')->from('categories'))
                            )
                            ->icon('heroicon-o-folder'),
                        QuantityResource::getTraceabilitySettings()->enable_lots_serial_numbers
                            ? RelationshipConstraint::make('lot')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.lot'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-rectangle-stack')
                            : null,
                        QuantityResource::getOperationSettings()->enable_packages
                            ? RelationshipConstraint::make('package')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.package'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-cube')
                            : null,
                        NumberConstraint::make('quantity')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.on-hand-quantity'))
                            ->icon('heroicon-o-scale'),
                        NumberConstraint::make('difference')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.difference-quantity'))
                            ->icon('heroicon-o-scale'),
                        RelationshipConstraint::make('user')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.user'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        DateConstraint::make('incoming_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.incoming-at')),
                        DateConstraint::make('scheduled_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.scheduled-at')),
                        DateConstraint::make('created_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.updated-at')),
                        RelationshipConstraint::make('company')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ])->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $product = Product::find($data['product_id']);

                        $data['location_id'] = $data['location_id'] ?? Warehouse::query()->when($product->company_id, fn ($query, $scopedCompanyId) => $query->where(owned_by_company($scopedCompanyId)))->value('lot_stock_location_id');

                        $data['company_id'] = $product->company_id;

                        $data['inventory_quantity_set'] = true;

                        $data['inventory_diff_quantity'] = $data['counted_quantity'];

                        $data['scheduled_at'] = now()->setDay(QuantityResource::getOperationSettings()->annual_inventory_day)->setMonth(QuantityResource::getOperationSettings()->annual_inventory_month);

                        return $data;
                    })
                    ->before(function (CreateAction $action, array $data) {
                        $existingQuantity = ProductQuantity::where('location_id', $data['location_id'] ?? Warehouse::query()->when(Product::find($data['product_id'])?->company_id, fn ($query, $scopedCompanyId) => $query->where(owned_by_company($scopedCompanyId)))->value('lot_stock_location_id'))
                            ->where('product_id', $data['product_id'])
                            ->where('package_id', $data['package_id'] ?? null)
                            ->where('lot_id', $data['lot_id'] ?? null)
                            ->exists();

                        if ($existingQuantity) {
                            Notification::make()
                                ->title(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.before.notification.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.before.notification.body'))
                                ->warning()
                                ->send();

                            $action->halt();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                Action::make('apply')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.actions.apply.label'))
                    ->icon('heroicon-o-check')
                    ->visible(fn (ProductQuantity $record) => $record->inventory_quantity_set)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.actions.apply.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.actions.apply.notification.body')),
                    )
                    ->action(function (ProductQuantity $record) {
                        $countedQuantity = $record->counted_quantity;

                        $record->update([
                            'quantity'               => $countedQuantity,
                            'counted_quantity'       => 0,
                            'inventory_quantity_set' => false,
                        ]);
                    }),
                Action::make('clear')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.actions.clear.label'))
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (ProductQuantity $record) => $record->inventory_quantity_set)
                    ->color('gray')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.actions.clear.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.actions.clear.notification.body')),
                    )
                    ->action(function (ProductQuantity $record) {
                        $record->update([
                            'inventory_quantity_set'  => false,
                            'counted_quantity'        => 0,
                            'inventory_diff_quantity' => 0,
                        ]);
                    }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('location', function (Builder $query) {
                    $query->whereIn('type', [LocationType::INTERNAL, LocationType::TRANSIT]);
                });

                $query->whereHas('product', function (Builder $query) {
                    $query->whereNull('deleted_at');
                });
            });
    }
}
