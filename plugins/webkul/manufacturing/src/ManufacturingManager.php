<?php

namespace Webkul\Manufacturing;

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
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Product\Enums\ProductType;

class ManufacturingManager
{
    public function confirmManufacturingOrder(Order $order)
    {
        $orderVals = [];

        if ($order->bill_of_material_id) {
            $orderVals['consumption'] = $order->billOfMaterial->consumption;
        }

        if (
            $order->product_tracking === ProductTracking::SERIAL
            && $order->uom_id !== $order->product->uom_id
        ) {
            $orderVals['quantity'] = $order->uom->computeQuantity($order->quantity, $order->product->uom);

            $orderVals['uom_id'] = $order->product->uom_id;

            $order->finishedMoves
                ->filter(fn ($move) => $move->product_id === $order->product_id)
                ->each(function ($moveFinish) {
                    $moveFinish->update([
                        'product_uom_qty' => $moveFinish->uom->computeQuantity($moveFinish->product_uom_qty, $moveFinish->product->uom),
                        'uom_id'          => $moveFinish->product->uom_id,
                    ]);
                });
        }

        if (! empty($orderVals)) {
            $order->update($orderVals);
        }

        $order->rawMaterialMoves->sortBy('id')->each->adjustProcureMethod();

        $order->rawMaterialMoves->sortBy('id')->each(function ($move) {
            $move->adjustProcureMethod();

            $move->save();
        });

        $order->load('rawMaterialMoves', 'finishedMoves');

        $movesToConfirm = $order->rawMaterialMoves->merge($order->finishedMoves)->sortBy('id')->unique('id');

        $this->confirmMoves($movesToConfirm, merge: false);

        $this->confirmWorkOrders($order, $order->workOrders->sortBy('id'));

        $operationsToConfirm = $order->inventory_operations
            ->filter(fn ($operation) => ! in_array($operation->state, [MoveState::CANCELED, MoveState::DONE]));

        foreach ($operationsToConfirm as $operation) {
            InventoryFacade::confirmTransfer($operation, merge: false);
        }

        if ($order->state === ManufacturingOrderState::DRAFT) {
            $order->update(['state' => ManufacturingOrderState::CONFIRMED]);
        }

        OrderConfirmed::dispatch($order);

        return $order;
    }

    public function startManufacturingOrder(Order $order)
    {
        if ($order->state !== ManufacturingOrderState::CONFIRMED) {
            return $order;
        }

        $order->update(['state' => ManufacturingOrderState::PROGRESS]);

        OrderStarted::dispatch($order);

        return $order;
    }

    public function planManufacturingOrder(Order $order)
    {
        if ($order->state === ManufacturingOrderState::DRAFT) {
            $order = $this->confirmManufacturingOrder($order);
        }

        $order = $this->planWorkOrders($order);

        OrderPlanned::dispatch($order);

        return $order;
    }

    public function unplanManufacturingOrder(Order $order)
    {
        if ($order->workOrders->some(fn ($workOrder) => $workOrder->state === WorkOrderState::DONE)) {
            throw new \Exception(__('manufacturing::system.manufacturing-manager.unplan-order.work-orders-already-done'));
        }

        if ($order->workOrders->some(fn ($workOrder) => $workOrder->state === WorkOrderState::PROGRESS)) {
            throw new \Exception(__('manufacturing::system.manufacturing-manager.unplan-order.work-orders-already-started'));
        }

        $order->workOrders->each(function ($workOrder) {
            $workOrder->calendarLeave?->delete();

            $workOrder->update([
                'started_at'  => null,
                'finished_at' => null,
            ]);
        });

        $order->update(['is_planned' => false]);

        return $order;
    }

    public function doneManufacturingOrder(Order $order)
    {
        $order->workOrders->each->finish();

        $this->postInventory($order, cancelBackOrder: true);

        $order->rawMaterialMoves
            ->merge($order->finishedMoves)
            ->filter(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
            ->each->update([
                'state' => MoveState::DONE,
            ]);

        $order->update([
            'state'       => ManufacturingOrderState::DONE,
            'is_locked'   => true,
            'priority'    => '0',
            'finished_at' => now(),
        ]);

        OrderDone::dispatch($order);
    }

    public function cancelManufacturingOrder(Order $order)
    {
        $order->workOrders
            ->filter(fn ($workOrder) => ! in_array($workOrder->state, [WorkOrderState::DONE, WorkOrderState::CANCEL]))
            ->each(function ($workOrder) {
                $workOrder->calendarLeave?->delete();

                $workOrder->endAll(collect([$workOrder]));

                $workOrder->update(['state' => WorkOrderState::CANCEL]);
            });

        $finishMoves = $order->finishedMoves->filter(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]));

        $rawMaterialMoves = $order->rawMaterialMoves->filter(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]));

        InventoryFacade::cancelMoves($finishMoves->merge($rawMaterialMoves));

        $order->inventoryOperations
            ->filter(fn ($operation) => ! in_array($operation->state, [OperationState::DONE, OperationState::CANCELED]))
            ->each(function ($operation) {
                InventoryFacade::cancelTransfer($operation);
            });

        $order->refresh();

        $order->computeState();

        $order->save();

        if (
            ! in_array($order->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])
            && $order->billOfMaterial->consumption === BillOfMaterialConsumption::FLEXIBLE
        ) {
            $order->update(['state' => ManufacturingOrderState::DONE]);
        }

        OrderCanceled::dispatch($order);

        return $order;
    }

    public function confirmWorkOrders(Order $order, $workOrders)
    {
        $order->linkWorkOrdersAndMoves($workOrders);
    }

    public function confirmMoves($moves, $merge = false, $mergeInto = false)
    {
        $moves = $this->explodeMoves($moves);

        $mergeInto = $mergeInto ? $this->explodeMoves($mergeInto) : false;

        InventoryFacade::confirmMoves($moves, merge: $merge, mergeInto: $mergeInto);
    }

    public function planWorkOrders(Order $order, bool $replan = false)
    {
        if ($order->workOrders->isEmpty()) {
            $order->update(['is_planned' => true]);

            return $order;
        }

        $order->linkWorkOrdersAndMoves();

        $finalWorkOrders = $order->workOrders->filter(fn ($workOrder) => $workOrder->dependentWorkOrders->isEmpty());

        $finalWorkOrders->each(fn ($workOrder) => $workOrder->plan($replan));

        $workOrders = $order->workOrders->filter(
            fn ($workOrder) => ! in_array($workOrder->state, [WorkOrderState::DONE, WorkOrderState::CANCEL])
        );

        if ($workOrders->isEmpty()) {
            return $order;
        }

        $order->update([
            'started_at'  => $workOrders->min(fn ($workOrder) => $workOrder->refresh()->calendarLeave->date_from),
            'finished_at' => $workOrders->max(fn ($workOrder) => $workOrder->refresh()->calendarLeave->date_to),
        ]);

        return $order;
    }

    public function explodeMoves($moves)
    {
        $movesToReturn = collect();

        $movesToUnlink = collect();

        $phantomMovesValsList = [];

        foreach ($moves as $move) {
            if (
                ! $move->operation_type_id
                || (
                    $move->order_id
                    && $move->order->product_id === $move->product_id
                )
            ) {
                $movesToReturn->push($move);

                continue;
            }

            $bom = BillOfMaterial::bomFind(collect([$move->product]), companyId: $move->company_id, bomType: 'phantom')[$move->product_id] ?? null;

            if (! $bom) {
                $movesToReturn->push($move);

                continue;
            }

            if (float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding)) {
                $factor = $move->uom->computeQuantity($move->quantity, $bom->uom) / $bom->quantity;
            } else {
                $factor = $move->uom->computeQuantity($move->product_uom_qty, $bom->uom) / $bom->quantity;
            }

            [, $lines] = $bom->explode(
                $move->product,
                $factor,
                operationType: $bom->operationType
            );

            foreach ($lines as [$bomLine, $lineData]) {
                if (float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding)) {
                    $phantomMovesValsList = array_merge($phantomMovesValsList, $this->generatePhantomMove($move, $bomLine, 0, $lineData['qty']));
                } else {
                    $phantomMovesValsList = array_merge($phantomMovesValsList, $this->generatePhantomMove($move, $bomLine, $lineData['qty'], 0));
                }
            }

            $movesToUnlink->push($move);
        }

        if (! empty($phantomMovesValsList)) {
            $phantomMoves = collect(array_map(fn ($vals) => Move::create($vals), $phantomMovesValsList));

            $phantomMoves->each->adjustProcureMethod();

            $movesToReturn = $movesToReturn->merge($this->explodeMoves($phantomMoves));
        }

        $movesToUnlink->each(function ($move) {
            $move->update(['quantity' => 0]);

            InventoryFacade::cancelMoves(collect([$move]));

            $move->delete();
        });

        return $movesToReturn;
    }

    public function preparePhantomMoveValues($move, $bomLine, $productQty, $quantityDone): array
    {
        return [
            'operation_id'    => $move->operation?->id ?? null,
            'product_id'      => $bomLine->product->id,
            'product_uom'     => $bomLine->uom->id,
            'product_uom_qty' => $productQty,
            'quantity'        => $quantityDone,
            'name'            => $move->name,
            'is_picked'       => $move->is_picked,
            'bom_line_id'     => $bomLine->id,
        ];
    }

    public function generatePhantomMove($move, $bomLine, $productQty, $quantityDone): array
    {
        $values = [];

        if ($bomLine->product->type === ProductType::GOODS) {
            $values = [$move->replicate()->fill(
                $this->preparePhantomMoveValues($move, $bomLine, $productQty, $quantityDone)
            )->toArray()];

            if ($move->state === MoveState::ASSIGNED) {
                foreach ($values as &$value) {
                    $value['state'] = MoveState::ASSIGNED;
                }
            }
        }

        return $values;
    }

    public function postInventory(Order $order, bool $cancelBackOrder = false): bool
    {
        $movesToDo = collect();

        $movesNotToDo = collect();

        $movesToCancel = collect();

        foreach ($order->rawMaterialMoves as $move) {
            if ($move->state === MoveState::DONE) {
                $movesNotToDo->push($move->id);
            } elseif (! $move->is_picked) {
                $movesToCancel->push($move->id);
            } elseif ($move->state !== MoveState::CANCELED) {
                $movesToDo->push($move->id);
            }
        }

        InventoryFacade::doneMoves(Move::whereIn('id', $movesToDo->all())->get(), cancelBackOrder: $cancelBackOrder);

        InventoryFacade::cancelMoves(Move::whereIn('id', $movesToCancel->all())->get());

        $movesToDo = $order->rawMaterialMoves
            ->filter(fn ($move) => $move->state === MoveState::DONE)
            ->filter(fn ($move) => ! $movesNotToDo->contains($move->id));

        $finishMoves = $order->finishedMoves->filter(
            fn ($move) => $move->product_id === $order->product_id
                && ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
        );

        foreach ($finishMoves as $move) {
            $move->update([
                'quantity' => float_round(
                    $order->quantity_producing - $order->quantity_produced,
                    precisionRounding: $order->uom->rounding,
                    roundingMethod: 'HALF-UP'
                ),
            ]);

            if ($order->producing_lot_id) {
                $move->lines->each->update(['lot_id' => $order->producing_lot_id]);
            }
        }

        foreach ($order->workOrders as $workOrder) {
            $expectedDuration = $workOrder->expected_duration;

            if (! in_array($workOrder->state, [WorkOrderState::DONE, WorkOrderState::CANCEL])) {
                $expectedDuration = $workOrder->getDurationExpected();
            }

            if ($workOrder->duration == 0.0) {
                $workOrder->update([
                    'expected_duration' => $expectedDuration,
                    'duration'          => $expectedDuration,
                    'duration_per_unit' => round($expectedDuration / max($workOrder->quantity_produced, 1), 2),
                ]);

                $workOrder->refresh();

                $workOrder->setDuration();
            }
        }

        $movesToFinish = $order->finishedMoves()->get()->filter(
            fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
        );

        $movesToFinish->each->update(['is_picked' => true]);

        $movesToFinish = InventoryFacade::doneMoves($movesToFinish, cancelBackOrder: $cancelBackOrder);

        return true;
    }
}
