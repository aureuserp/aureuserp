<?php

namespace Webkul\Manufacturing\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Events\OrderCanceled;
use Webkul\Manufacturing\Events\OrderConfirmed;
use Webkul\Manufacturing\Events\OrderDone;
use Webkul\Manufacturing\Events\OrderPlanned;
use Webkul\Manufacturing\Events\OrderStarted;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkOrder;

class OrderWorkflow
{
    public function confirm(Order $order): Order
    {
        $this->applyConfirmationDefaults($order);

        $order->rawMaterialMoves->sortBy('id')->each(function (Move $move) {
            $move->resolveProcureMethod();

            $move->save();
        });

        $order->load('rawMaterialMoves', 'finishedMoves');

        $this->confirmMoves(
            $order->rawMaterialMoves->merge($order->finishedMoves)->sortBy('id')->unique('id'),
            merge: false
        );

        $order->linkWorkOrdersAndMoves($order->workOrders->sortBy('id'));

        $order->inventory_operations
            ->filter(fn ($operation) => ! in_array($operation->state, [MoveState::CANCELED, MoveState::DONE]))
            ->each(fn ($operation) => InventoryFacade::confirmTransfer($operation));

        if ($order->state === ManufacturingOrderState::DRAFT) {
            $order->update(['state' => ManufacturingOrderState::CONFIRMED]);
        }

        OrderConfirmed::dispatch($order);

        return $order;
    }

    protected function applyConfirmationDefaults(Order $order): void
    {
        $attributes = [];

        if ($order->bill_of_material_id) {
            $attributes['consumption'] = $order->billOfMaterial->consumption;
        }

        if ($this->needsSerialUomAlignment($order)) {
            $attributes['quantity'] = $order->uom->computeQuantity($order->quantity, $order->product->uom);

            $attributes['uom_id'] = $order->product->uom_id;

            $order->finishedMoves
                ->filter(fn (Move $move) => $move->product_id === $order->product_id)
                ->each(fn (Move $move) => $move->update([
                    'product_uom_qty' => $move->uom->computeQuantity($move->product_uom_qty, $move->product->uom),
                    'uom_id'          => $move->product->uom_id,
                ]));
        }

        if ($attributes !== []) {
            $order->update($attributes);
        }
    }

    protected function needsSerialUomAlignment(Order $order): bool
    {
        return $order->product_tracking === ProductTracking::SERIAL
            && $order->uom_id !== $order->product->uom_id;
    }

    public function start(Order $order): Order
    {
        if ($order->state !== ManufacturingOrderState::CONFIRMED) {
            return $order;
        }

        $order->update(['state' => ManufacturingOrderState::PROGRESS]);

        OrderStarted::dispatch($order);

        return $order;
    }

    public function plan(Order $order): Order
    {
        if ($order->state === ManufacturingOrderState::DRAFT) {
            $order = $this->confirm($order);
        }

        $order = $this->scheduleWorkOrders($order);

        OrderPlanned::dispatch($order);

        return $order;
    }

    public function unplan(Order $order): Order
    {
        $this->assertUnplannable($order);

        $order->workOrders->each(function (WorkOrder $workOrder) {
            $workOrder->calendarLeave?->delete();

            $workOrder->update(['started_at' => null, 'finished_at' => null]);
        });

        $order->update(['is_planned' => false]);

        return $order;
    }

    protected function assertUnplannable(Order $order): void
    {
        if ($order->workOrders->some(fn (WorkOrder $workOrder) => $workOrder->state === WorkOrderState::DONE)) {
            throw new \Exception(__('manufacturing::system.manufacturing-manager.unplan-order.work-orders-already-done'));
        }

        if ($order->workOrders->some(fn (WorkOrder $workOrder) => $workOrder->state === WorkOrderState::PROGRESS)) {
            throw new \Exception(__('manufacturing::system.manufacturing-manager.unplan-order.work-orders-already-started'));
        }
    }

    public function complete(Order $order): void
    {
        $order->workOrders->each->finish();

        app(ProductionRecorder::class)->record($order, cancelBackorder: true);

        $order->rawMaterialMoves
            ->merge($order->finishedMoves)
            ->filter(fn (Move $move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
            ->each->update(['state' => MoveState::DONE]);

        $order->update([
            'state'       => ManufacturingOrderState::DONE,
            'is_locked'   => true,
            'priority'    => '0',
            'finished_at' => now(),
        ]);

        OrderDone::dispatch($order);
    }

    public function cancel(Order $order): Order
    {
        $order->workOrders
            ->filter(fn (WorkOrder $workOrder) => ! in_array($workOrder->state, [WorkOrderState::DONE, WorkOrderState::CANCEL]))
            ->each(function (WorkOrder $workOrder) {
                $workOrder->calendarLeave?->delete();

                $workOrder->endAll(collect([$workOrder]));

                $workOrder->update(['state' => WorkOrderState::CANCEL]);
            });

        $openMoves = $order->finishedMoves
            ->merge($order->rawMaterialMoves)
            ->filter(fn (Move $move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]));

        InventoryFacade::cancelMoves($openMoves);

        $order->inventoryOperations
            ->filter(fn ($operation) => ! in_array($operation->state, [OperationState::DONE, OperationState::CANCELED]))
            ->each(fn ($operation) => InventoryFacade::cancelTransfer($operation));

        $order->refresh();

        $order->computeState();

        $order->save();

        $this->closeFlexibleOrder($order);

        OrderCanceled::dispatch($order);

        return $order;
    }

    protected function closeFlexibleOrder(Order $order): void
    {
        $stillOpen = ! in_array($order->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL]);

        if ($stillOpen && $order->billOfMaterial->consumption === BillOfMaterialConsumption::FLEXIBLE) {
            $order->update(['state' => ManufacturingOrderState::DONE]);
        }
    }

    public function confirmMoves(Collection $moves, bool $merge = false, ?Collection $mergeInto = null): void
    {
        $kits = app(KitExpander::class);

        InventoryFacade::confirmMoves(
            $kits->expandMoves($moves),
            merge: $merge,
            mergeInto: $mergeInto ? $kits->expandMoves($mergeInto) : null
        );
    }

    public function scheduleWorkOrders(Order $order, bool $replan = false): Order
    {
        if ($order->workOrders->isEmpty()) {
            $order->update(['is_planned' => true]);

            return $order;
        }

        $order->linkWorkOrdersAndMoves();

        $order->workOrders
            ->filter(fn (WorkOrder $workOrder) => $workOrder->dependentWorkOrders->isEmpty())
            ->each(fn (WorkOrder $workOrder) => $workOrder->plan($replan));

        $scheduled = $order->workOrders->filter(
            fn (WorkOrder $workOrder) => ! in_array($workOrder->state, [WorkOrderState::DONE, WorkOrderState::CANCEL])
        );

        if ($scheduled->isEmpty()) {
            return $order;
        }

        $order->update([
            'started_at'  => $scheduled->min(fn (WorkOrder $workOrder) => $workOrder->refresh()->calendarLeave->date_from),
            'finished_at' => $scheduled->max(fn (WorkOrder $workOrder) => $workOrder->refresh()->calendarLeave->date_to),
        ]);

        return $order;
    }
}
