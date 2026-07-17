<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Models\Product;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Models\PutawayRule;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Inventory\Models\StorageCategoryCapacity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class InventoryHelper
{
    public static function unitsUom(): UOM
    {
        return UOM::query()->where('name', 'Units')->firstOrFail();
    }

    public static function dozensUom(): UOM
    {
        return UOM::query()->where('name', 'Dozens')->firstOrFail();
    }

    public static function company(): Company
    {
        return Company::query()->firstOrFail();
    }

    public static function actingAsAdmin(): User
    {
        $user = User::query()->firstOrFail();

        Auth::login($user);

        return $user;
    }

    public static function warehouse(
        ReceptionStep $reception = ReceptionStep::ONE_STEP,
        DeliveryStep $delivery = DeliveryStep::ONE_STEP,
    ): Warehouse {
        $warehouse = Warehouse::factory()->create([
            'company_id' => static::company()->id,
            'sort'       => 1,
        ]);

        $warehouse->update([
            'reception_steps' => $reception,
            'delivery_steps'  => $delivery,
            'sort'            => 2,
        ]);

        return $warehouse->refresh();
    }

    public static function inventoryAdjustmentLocation(): Location
    {
        return Location::where('type', LocationType::INVENTORY)
            ->where('is_scrap', false)
            ->firstOrFail();
    }

    public static function addProductQuantity(Product $product, Location $location, float $quantity, ?int $lotId = null, ?int $packageId = null): ProductQuantity
    {
        return ProductQuantity::factory()->create([
            'product_id'              => $product->id,
            'location_id'             => $location->id,
            'lot_id'                  => $lotId,
            'package_id'              => $packageId,
            'quantity'                => $quantity,
            'inventory_diff_quantity' => $quantity,
            'company_id'              => static::company()->id,
        ]);
    }

    public static function editProductQuantity(ProductQuantity $quant, float $newQuantity): void
    {
        $quant->update([
            'quantity'                => $newQuantity,
            'inventory_diff_quantity' => $newQuantity - (float) $quant->quantity,
        ]);
    }

    public static function applyInventoryAdjustment(ProductQuantity $quant, float $counted): void
    {
        $quant->update([
            'inventory_diff_quantity' => $counted - (float) $quant->quantity,
            'inventory_quantity_set'  => true,
        ]);

        $quant->update([
            'quantity'               => $counted,
            'inventory_quantity_set' => false,
        ]);
    }

    public static function opTypeArchived(?int $id): bool
    {
        return $id
            ? (OperationType::withTrashed()->find($id)?->trashed() ?? true)
            : true;
    }

    public static function locationArchived(?int $id): bool
    {
        return $id
            ? (Location::withTrashed()->find($id)?->trashed() ?? true)
            : true;
    }

    public static function routeArchived(?int $id): bool
    {
        return $id
            ? (Route::withTrashed()->find($id)?->trashed() ?? true)
            : true;
    }

    public static function activeRuleExists(int $sourceId, int $destinationId, int $operationTypeId): bool
    {
        return Rule::query()
            ->where('source_location_id', $sourceId)
            ->where('destination_location_id', $destinationId)
            ->where('operation_type_id', $operationTypeId)
            ->whereNull('deleted_at')
            ->exists();
    }

    public static function scrapLocation(): Location
    {
        return Location::factory()->create([
            'name'       => 'Scrap',
            'type'       => LocationType::INVENTORY,
            'company_id' => static::company()->id,
        ]);
    }

    public static function scrap(Product $product, Location $source, Location $destination, float $qty, ?int $lotId = null): Scrap
    {
        return Scrap::factory()->create([
            'product_id'              => $product->id,
            'uom_id'                  => $product->uom_id,
            'lot_id'                  => $lotId,
            'qty'                     => $qty,
            'source_location_id'      => $source->id,
            'destination_location_id' => $destination->id,
            'company_id'              => static::company()->id,
        ]);
    }

    public static function dropship(array $lines): Operation
    {
        $operation = Operation::factory()->dropship()->create([
            'state'        => OperationState::DRAFT,
            'move_type'    => MoveType::DIRECT,
            'scheduled_at' => now(),
            'company_id'   => static::company()->id,
        ]);

        foreach ($lines as [$product, $demand]) {
            Move::factory()->demand($demand)->create([
                'name'                    => $product->name,
                'state'                   => MoveState::DRAFT,
                'product_id'              => $product->id,
                'uom_id'                  => $product->uom_id,
                'operation_id'            => $operation->id,
                'operation_type_id'       => $operation->operation_type_id,
                'source_location_id'      => $operation->source_location_id,
                'destination_location_id' => $operation->destination_location_id,
                'company_id'              => static::company()->id,
            ]);
        }

        return $operation->refresh();
    }

    public static function enableLocations(): void
    {
        $settings = app(WarehouseSettings::class);

        $settings->enable_locations = true;

        $settings->save();
    }

    public static function internalTransfer(Warehouse $warehouse, Location $source, Location $destination, array $lines): Operation
    {
        $operation = Operation::factory()->create([
            'operation_type_id'       => $warehouse->internal_type_id,
            'source_location_id'      => $source->id,
            'destination_location_id' => $destination->id,
            'state'                   => OperationState::DRAFT,
            'move_type'               => MoveType::DIRECT,
            'scheduled_at'            => now(),
            'company_id'              => static::company()->id,
        ]);

        foreach ($lines as [$product, $demand]) {
            Move::factory()->demand($demand)->create([
                'name'                    => $product->name,
                'state'                   => MoveState::DRAFT,
                'product_id'              => $product->id,
                'uom_id'                  => $product->uom_id,
                'operation_id'            => $operation->id,
                'operation_type_id'       => $warehouse->internal_type_id,
                'source_location_id'      => $source->id,
                'destination_location_id' => $destination->id,
                'company_id'              => static::company()->id,
            ]);
        }

        return $operation->refresh();
    }

    public static function sublocation(Location $parent, ?string $name = null, ?StorageCategory $storageCategory = null): Location
    {
        return Location::factory()->create([
            'name'                => $name ?? fake()->unique()->word(),
            'type'                => LocationType::INTERNAL,
            'parent_id'           => $parent->id,
            'warehouse_id'        => $parent->warehouse_id,
            'storage_category_id' => $storageCategory?->id,
            'company_id'          => static::company()->id,
        ]);
    }

    public static function putawayRule(Location $in, Location $out, ?Product $product = null, ?StorageCategory $storageCategory = null): PutawayRule
    {
        return PutawayRule::factory()->create([
            'in_location_id'      => $in->id,
            'out_location_id'     => $out->id,
            'product_id'          => $product?->id,
            'storage_category_id' => $storageCategory?->id,
            'company_id'          => static::company()->id,
        ]);
    }

    public static function storageCategory(): StorageCategory
    {
        return StorageCategory::factory()->create([
            'company_id' => static::company()->id,
        ]);
    }

    public static function storageCategoryCapacity(StorageCategory $category, Product $product, float $qty): StorageCategoryCapacity
    {
        return StorageCategoryCapacity::factory()->create([
            'storage_category_id' => $category->id,
            'product_id'          => $product->id,
            'qty'                 => $qty,
        ]);
    }

    public static function setReservationMethod(OperationType $operationType, ReservationMethod $method): OperationType
    {
        $operationType->update(['reservation_method' => $method]);

        return $operationType->refresh();
    }

    public static function product(array $overrides = []): Product
    {
        $uom = static::unitsUom();

        return Product::factory()->create(array_merge([
            'is_storable' => true,
            'uom_id'      => $uom->id,
            'uom_po_id'   => $uom->id,
            'company_id'  => static::company()->id,
        ], $overrides));
    }

    public static function stockUp(
        BaseProduct $product,
        Location $location,
        float $quantity,
        ?int $lotId = null,
        ?int $packageId = null,
    ): ProductQuantity {
        return ProductQuantity::factory()->create([
            'product_id'        => $product->id,
            'location_id'       => $location->id,
            'lot_id'            => $lotId,
            'package_id'        => $packageId,
            'quantity'          => $quantity,
            'reserved_quantity' => 0,
            'incoming_at'       => now(),
            'company_id'        => static::company()->id,
        ]);
    }

    public static function operation(OperationType $operationType, array $lines): Operation
    {
        $operation = Operation::factory()->create([
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $operationType->source_location_id,
            'destination_location_id' => $operationType->destination_location_id,
            'state'                   => OperationState::DRAFT,
            'move_type'               => MoveType::DIRECT,
            'scheduled_at'            => now(),
            'company_id'              => static::company()->id,
        ]);

        foreach ($lines as $line) {
            [$product, $demand] = $line;

            $uom = $line[2] ?? null;

            Move::factory()->demand($demand, $uom)->create([
                'name'                    => $product->name,
                'state'                   => MoveState::DRAFT,
                'product_id'              => $product->id,
                'uom_id'                  => $uom?->id ?? $product->uom_id,
                'operation_id'            => $operation->id,
                'operation_type_id'       => $operationType->id,
                'source_location_id'      => $operation->source_location_id,
                'destination_location_id' => $operation->destination_location_id,
                'company_id'              => static::company()->id,
            ]);
        }

        return $operation->refresh();
    }

    public static function receipt(Warehouse $warehouse, array $lines): Operation
    {
        return static::operation($warehouse->inType, $lines);
    }

    public static function delivery(Warehouse $warehouse, array $lines): Operation
    {
        $operationType = $warehouse->delivery_steps === DeliveryStep::ONE_STEP
            ? $warehouse->outType
            : $warehouse->pickType;

        return static::operation($operationType, $lines);
    }

    public static function pick(Move $move, float $quantity): Move
    {
        $move->update(['quantity' => $quantity]);

        return $move->refresh();
    }

    public static function lotTrackedProduct(array $overrides = []): Product
    {
        return static::product(array_merge(['tracking' => ProductTracking::LOT], $overrides));
    }

    public static function serialTrackedProduct(array $overrides = []): Product
    {
        return static::product(array_merge(['tracking' => ProductTracking::SERIAL], $overrides));
    }

    public static function trackLots(OperationType $operationType, bool $create = true, bool $existing = true): OperationType
    {
        $operationType->update([
            'use_create_lots'   => $create,
            'use_existing_lots' => $existing,
        ]);

        return $operationType->refresh();
    }

    public static function lot(BaseProduct $product, string $name): Lot
    {
        return Lot::factory()->create([
            'name'       => $name,
            'product_id' => $product->id,
            'uom_id'     => $product->uom_id,
            'company_id' => static::company()->id,
        ]);
    }

    public static function nameLines(Move $move, array $names): void
    {
        $move->refresh()->lines->values()->each(function (MoveLine $line, int $index) use ($names) {
            if (! array_key_exists($index, $names)) {
                return;
            }

            $line->update(['lot_name' => $names[$index]]);
        });
    }

    public static function package(PackageUse $use = PackageUse::DISPOSABLE, ?Location $location = null, bool $typed = true): Package
    {
        return Package::factory()->create([
            'package_use'     => $use,
            'location_id'     => $location?->id,
            'package_type_id' => $typed ? PackageType::factory() : null,
            'company_id'      => static::company()->id,
        ]);
    }

    public static function lotsOf(BaseProduct $product): array
    {
        return Lot::query()
            ->where('product_id', $product->id)
            ->orderBy('id')
            ->pluck('name')
            ->all();
    }

    public static function operationCount(Warehouse $warehouse): int
    {
        return Operation::query()
            ->whereIn('operation_type_id', array_filter([
                $warehouse->in_type_id,
                $warehouse->out_type_id,
                $warehouse->pick_type_id,
                $warehouse->pack_type_id,
                $warehouse->qc_type_id,
                $warehouse->store_type_id,
                $warehouse->internal_type_id,
                $warehouse->xdock_type_id,
            ]))
            ->count();
    }

    public static function backorderOf(Operation $operation): ?Operation
    {
        return Operation::query()->where('back_order_id', $operation->id)->first();
    }

    public static function returnOf(Operation $operation): ?Operation
    {
        return Operation::query()->where('return_id', $operation->id)->first();
    }

    public static function quantOf(BaseProduct $product, Location $location, ?int $lotId = null): ?ProductQuantity
    {
        return ProductQuantity::query()
            ->where('product_id', $product->id)
            ->where('location_id', $location->id)
            ->where('lot_id', $lotId)
            ->first();
    }

    public static function onHand(BaseProduct $product, Location $location): float
    {
        return (float) ProductQuantity::query()
            ->where('product_id', $product->id)
            ->where('location_id', $location->id)
            ->sum('quantity');
    }

    public static function reserved(BaseProduct $product, Location $location): float
    {
        return (float) ProductQuantity::query()
            ->where('product_id', $product->id)
            ->where('location_id', $location->id)
            ->sum('reserved_quantity');
    }

    public static function onHandUnder(Product $product, Location $parent): float
    {
        return (float) static::quantsUnder($product, $parent)->sum('quantity');
    }

    public static function reservedUnder(Product $product, Location $parent): float
    {
        return (float) static::quantsUnder($product, $parent)->sum('reserved_quantity');
    }

    protected static function quantsUnder(Product $product, Location $parent)
    {
        return ProductQuantity::query()
            ->where('product_id', $product->id)
            ->whereHas('location', fn ($query) => $query->where('parent_path', 'like', $parent->parent_path.'%'));
    }

    public static function lineQuantities(Move $move): array
    {
        return $move->lines
            ->pluck('qty')
            ->map(fn ($qty) => (float) $qty)
            ->sort()
            ->values()
            ->all();
    }
}
