<?php

namespace Webkul\Manufacturing\Services;

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;

class ProductionRecorder
{
    public function record(Order $order, bool $cancelBackorder = false): bool
    {
        $this->consumeComponents($order, $cancelBackorder);

        $this->stampProducedQuantity($order);

        $this->backfillWorkOrderDurations($order);

        $this->produceOutput($order, $cancelBackorder);

        return true;
    }

    protected function consumeComponents(Order $order, bool $cancelBackorder): void
    {
        $toConsume = collect();

        $toCancel = collect();

        foreach ($order->rawMaterialMoves as $move) {
            if ($move->state === MoveState::DONE) {
                continue;
            }

            if (! $move->is_picked) {
                $toCancel->push($move->id);
            } elseif ($move->state !== MoveState::CANCELED) {
                $toConsume->push($move->id);
            }
        }

        InventoryFacade::completeMoves(
            Move::whereIn('id', $toConsume->all())->get(),
            cancelBackorder: $cancelBackorder
        );

        InventoryFacade::cancelMoves(Move::whereIn('id', $toCancel->all())->get());
    }

    protected function stampProducedQuantity(Order $order): void
    {
        $outputMoves = $order->finishedMoves->filter(
            fn (Move $move) => $move->product_id === $order->product_id
                && ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
        );

        $producedQuantity = float_round(
            $order->quantity_producing - $order->quantity_produced,
            precisionRounding: $order->uom->rounding,
            roundingMethod: 'HALF-UP'
        );

        foreach ($outputMoves as $move) {
            $move->update(['quantity' => $producedQuantity]);

            if ($order->producing_lot_id) {
                $move->lines->each->update(['lot_id' => $order->producing_lot_id]);
            }
        }
    }

    protected function backfillWorkOrderDurations(Order $order): void
    {
        foreach ($order->workOrders as $workOrder) {
            if ($workOrder->duration != 0.0) {
                continue;
            }

            $expectedDuration = in_array($workOrder->state, [WorkOrderState::DONE, WorkOrderState::CANCEL])
                ? $workOrder->expected_duration
                : $workOrder->getDurationExpected();

            $workOrder->update([
                'expected_duration' => $expectedDuration,
                'duration'          => $expectedDuration,
                'duration_per_unit' => round($expectedDuration / max($workOrder->quantity_produced, 1), 2),
            ]);

            $workOrder->refresh();

            $workOrder->setDuration();
        }
    }

    protected function produceOutput(Order $order, bool $cancelBackorder): void
    {
        $outputMoves = $order->finishedMoves()->get()->filter(
            fn (Move $move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
        );

        $outputMoves->each->update(['is_picked' => true]);

        InventoryFacade::completeMoves($outputMoves, cancelBackorder: $cancelBackorder);
    }
}
