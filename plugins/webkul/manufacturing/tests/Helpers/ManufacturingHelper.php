<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\OperationType;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\BillOfMaterialByproduct;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Warehouse;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class ManufacturingHelper
{
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

    public static function unitsUom(): UOM
    {
        return UOM::query()->where('name', 'Units')->firstOrFail();
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

    public static function multiStepWarehouse(ManufactureStep $step)
    {
        $warehouse = InventoryHelper::warehouse();

        $manufacturingWarehouse = Warehouse::query()->whereKey($warehouse->id)->first();

        $manufacturingWarehouse->update(['manufacture_steps' => $step]);

        $manufacturingWarehouse->syncManufacturingWarehouseConfiguration();

        return $warehouse->refresh();
    }

    public static function workCenter(array $overrides = []): WorkCenter
    {
        return WorkCenter::factory()->create(array_merge([
            'company_id' => static::company()->id,
        ], $overrides));
    }

    public static function bom(Product $product, array $components = [], array $overrides = []): BillOfMaterial
    {
        $bom = BillOfMaterial::factory()->create(array_merge([
            'product_id'  => $product->id,
            'uom_id'      => $product->uom_id,
            'quantity'    => 1,
            'consumption' => BillOfMaterialConsumption::FLEXIBLE,
            'company_id'  => static::company()->id,
        ], $overrides));

        foreach ($components as [$componentProduct, $qty]) {
            static::bomLine($bom, $componentProduct, $qty);
        }

        return $bom->refresh();
    }

    public static function bomLine(BillOfMaterial $bom, Product $product, float $qty): BillOfMaterialLine
    {
        return BillOfMaterialLine::create([
            'bill_of_material_id' => $bom->id,
            'product_id'          => $product->id,
            'uom_id'              => $product->uom_id,
            'quantity'            => $qty,
            'company_id'          => static::company()->id,
        ]);
    }

    public static function byproduct(BillOfMaterial $bom, Product $product, float $qty): BillOfMaterialByproduct
    {
        return BillOfMaterialByproduct::create([
            'bill_of_material_id' => $bom->id,
            'product_id'          => $product->id,
            'uom_id'              => $product->uom_id,
            'quantity'            => $qty,
            'cost_share'          => 0,
            'company_id'          => static::company()->id,
        ]);
    }

    public static function operation(BillOfMaterial $bom, WorkCenter $workCenter, array $overrides = []): Operation
    {
        return Operation::factory()->create(array_merge([
            'bill_of_material_id' => $bom->id,
            'work_center_id'      => $workCenter->id,
        ], $overrides));
    }

    public static function order($warehouse, Product $product, ?BillOfMaterial $bom, float $qty): Order
    {
        $operationType = OperationType::findOrFail($warehouse->manu_type_id);

        $order = Order::factory()->create([
            'state'                   => ManufacturingOrderState::DRAFT,
            'consumption'             => $bom?->consumption ?? BillOfMaterialConsumption::FLEXIBLE,
            'product_id'              => $product->id,
            'uom_id'                  => $product->uom_id,
            'bill_of_material_id'     => $bom?->id,
            'quantity'                => $qty,
            'quantity_producing'      => 0,
            'operation_type_id'       => $warehouse->manu_type_id,
            'source_location_id'      => $operationType->source_location_id,
            'destination_location_id' => $operationType->destination_location_id,
            'company_id'              => static::company()->id,
        ]);

        $order->refresh()->computeFinishedMoves();

        foreach ($order->getMovesRawValues() as $values) {
            Move::create($values);
        }

        return $order->refresh();
    }

    public static function addComponent(Order $order, Product $product, float $qty): Move
    {
        $values = $order->getMoveRawValues($product->id, $qty, $product->uom_id);

        return Move::create($values);
    }

    public static function workOrder(Order $order, WorkCenter $workCenter, ?Operation $operation = null, array $overrides = []): WorkOrder
    {
        return WorkOrder::factory()->create(array_merge([
            'manufacturing_order_id' => $order->id,
            'operation_id'           => $operation?->id ?? Operation::factory(['bill_of_material_id' => $order->bill_of_material_id, 'work_center_id' => $workCenter->id]),
            'work_center_id'         => $workCenter->id,
            'product_id'             => $order->product_id,
            'uom_id'                 => $order->uom_id,
        ], $overrides));
    }

    public static function logProductivity(WorkOrder $workOrder, float $duration, string $type = 'productive'): WorkCenterProductivityLog
    {
        return WorkCenterProductivityLog::factory()->{$type}($duration)->create([
            'work_order_id'  => $workOrder->id,
            'work_center_id' => $workOrder->work_center_id,
            'company_id'     => static::company()->id,
        ]);
    }

    public static function startWorkOrder(WorkOrder $workOrder): WorkOrder
    {
        $workOrder->refresh()->start();

        return $workOrder->refresh();
    }

    public static function pauseWorkOrder(WorkOrder $workOrder): WorkOrder
    {
        $workOrder->refresh()->pending();

        return $workOrder->refresh();
    }

    public static function finishWorkOrder(WorkOrder $workOrder): WorkOrder
    {
        $workOrder->refresh()->finish();

        return $workOrder->refresh();
    }

    public static function confirm(Order $order): Order
    {
        return Manufacturing::confirmManufacturingOrder($order->refresh());
    }

    public static function produce(Order $order, ?float $qty = null): Order
    {
        $order = $order->refresh();

        $order->update(['quantity_producing' => $qty ?? $order->quantity]);

        Manufacturing::doneManufacturingOrder($order->refresh());

        return $order->refresh();
    }

    public static function validateAuxiliaryOperations(Order $order): void
    {
        for ($i = 0; $i < 6; $i++) {
            $ready = $order->refresh()->inventoryOperations
                ->first(fn ($operation) => ! in_array($operation->state, [OperationState::DONE, OperationState::CANCELED])
                    && $operation->moves->contains(fn ($move) => in_array($move->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])));

            if (! $ready) {
                break;
            }

            InventoryFacade::doneTransfer($ready->refresh());
        }
    }
}
