<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Models\Product as BaseProduct;

class UpdateQuantityAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'updateQuantity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('inventories::filament/clusters/products/resources/product/actions/update-quantity.label'))
            ->modalHeading(__('inventories::filament/clusters/products/resources/product/actions/update-quantity.modal-heading'))
            ->url(function (
                BaseProduct $record,
                OperationSettings $operationSettings,
                TraceabilitySettings $traceabilitySettings,
                WarehouseSettings $warehouseSettings,
            ): ?string {
                if (
                    $operationSettings->enable_packages
                    || $warehouseSettings->enable_locations
                    || (
                        $traceabilitySettings->enable_lots_serial_numbers
                        && $record->tracking != ProductTracking::QTY
                    )
                ) {
                    return ProductResource::getUrl('quantities', ['record' => $record]);
                }

                return null;
            })
            ->schema(fn (BaseProduct $record): array => [
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/actions/update-quantity.form.fields.product'))
                    ->required()
                    ->options($record->variants->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $product = Product::find($get('product_id'));

                        $set('quantity', $product?->available_qty ?? 0);
                    })
                    ->visible((bool) $record->is_configurable),
                TextInput::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/actions/update-quantity.form.fields.on-hand-qty'))
                    ->numeric()
                    ->maxValue(99999999999)
                    ->required()
                    ->live()
                    ->suffix($record->uom->name)
                    ->default(fn (): int|float => ! $record->is_configurable ? (Product::find($record->getKey())?->available_qty ?? 0) : 0),
            ])
            ->modalSubmitActionLabel(__('inventories::filament/clusters/products/resources/product/actions/update-quantity.modal-submit-action-label'))
            ->visible(fn (BaseProduct $record): bool => (bool) $record->is_storable)
            ->action(function (BaseProduct $record, array $data): void {
                $record = isset($data['product_id'])
                    ? Product::find($data['product_id'])
                    : Product::find($record->getKey());

                $previousQuantity = $record->available_qty;

                if ($previousQuantity == $data['quantity']) {
                    return;
                }

                $warehouse = Warehouse::first();

                $productQuantity = ProductQuantity::where('product_id', $record->id)
                    ->where('location_id', $data['location_id'] ?? $warehouse->lot_stock_location_id)
                    ->first();

                if ($productQuantity) {
                    $productQuantity->update([
                        'quantity'                => $data['quantity'],
                        'inventory_diff_quantity' => $data['quantity'] - $previousQuantity,
                    ]);

                    return;
                }

                ProductQuantity::create([
                    'product_id'              => $record->id,
                    'company_id'              => $record->company_id,
                    'location_id'             => $data['location_id'] ?? $warehouse->lot_stock_location_id,
                    'package_id'              => $data['package_id'] ?? null,
                    'lot_id'                  => $data['lot_id'] ?? null,
                    'quantity'                => $data['quantity'],
                    'inventory_diff_quantity' => $data['quantity'],
                ]);
            });
    }
}
