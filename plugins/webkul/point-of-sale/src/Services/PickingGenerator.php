<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\PointOfSale\Events\OrderOperationFailed;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\Product\Enums\ProductType;

class PickingGenerator
{
    public function __construct(
        protected LotResolver $lots,
    ) {}

    public function generateFor(Order $order): ?Operation
    {
        $order->loadMissing(['lines.product', 'lines.lots', 'config.operationType', 'config.returnOperationType']);

        $lines = $order->lines->filter(
            fn (OrderLine $line): bool => $this->movesStock($line)
        );

        if ($lines->isEmpty()) {
            return null;
        }

        $primary = null;

        foreach ([[false, $lines->filter(fn (OrderLine $line): bool => (float) $line->qty > 0)],
            [true, $lines->filter(fn (OrderLine $line): bool => (float) $line->qty < 0)]] as [$isReturn, $set]) {
            if ($set->isEmpty()) {
                continue;
            }

            $operationType = $this->operationTypeFor($order, $isReturn);

            if (! $operationType) {
                $this->flagMissingOperationType($order);

                continue;
            }

            $operation = $this->buildOperation($order, $operationType, $set);

            $this->forceComplete($order, $operation);

            $primary ??= $operation;
        }

        return $primary;
    }

    public function retry(Order $order): ?Operation
    {
        $operation = $order->operation;

        if (! $operation || $operation->state === OperationState::DONE) {
            return $operation;
        }

        $completed = $this->forceComplete($order, $operation);

        if ($completed) {
            $order->forceFill(['has_failed_operation' => false])->save();
        }

        return $operation->refresh();
    }

    protected function movesStock(OrderLine $line): bool
    {
        return $line->product?->type === ProductType::GOODS
            && $line->product?->is_storable
            && ! float_is_zero((float) $line->qty, precisionDigits: 4);
    }

    protected function operationTypeFor(Order $order, bool $isReturn): ?OperationType
    {
        return $isReturn
            ? $order->config?->returnOperationType
            : $order->config?->operationType;
    }

    protected function buildOperation(Order $order, OperationType $operationType, Collection $lines): Operation
    {
        $operation = Operation::create([
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $operationType->source_location_id,
            'destination_location_id' => $operationType->destination_location_id,
            'state'                   => OperationState::DRAFT,
            'move_type'               => MoveType::DIRECT,
            'scheduled_at'            => now(),
            'partner_id'              => $order->partner_id,
            'origin'                  => $order->name ?? $order->reference,
            'user_id'                 => $order->user_id,
            'company_id'              => $order->company_id,
        ]);

        $lines->each(function (OrderLine $line) use ($operation, $operationType, $order): void {
            $move = Move::create([
                'name'                    => $line->full_product_name ?? $line->product?->name,
                'state'                   => MoveState::DRAFT,
                'product_id'              => $line->product_id,
                'uom_id'                  => $line->uom_id ?? $line->product?->uom_id,
                'product_uom_qty'         => abs((float) $line->qty),
                'operation_id'            => $operation->id,
                'operation_type_id'       => $operationType->id,
                'source_location_id'      => $operation->source_location_id,
                'destination_location_id' => $operation->destination_location_id,
                'partner_id'              => $order->partner_id,
                'scheduled_at'            => now(),
                'price_unit'              => (float) $line->price_unit,
                'company_id'              => $order->company_id,
            ]);

            $this->lots->applyTo($order, $move, $line);
        });

        return $operation->refresh();
    }

    protected function flagMissingOperationType(Order $order): void
    {
        $order->forceFill(['has_failed_operation' => true])->save();

        $order->session()->update(['has_failed_operations' => true]);

        OrderOperationFailed::dispatch($order, null, new PosConfigurationException(
            __('point-of-sale::system.picking.operation-type-missing', ['order' => $order->reference ?? $order->name])
        ));
    }

    protected function forceComplete(Order $order, Operation $operation): bool
    {
        DB::beginTransaction();

        try {
            Inventory::confirmTransfer($operation);

            $operation->refresh()->moves->each(fn (Move $move) => $move->update([
                'quantity'  => $move->product_uom_qty,
                'is_picked' => true,
            ]));

            Inventory::completeTransfer($operation->refresh(), cancelBackorder: true);

            DB::commit();

            return true;
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            $order->forceFill(['has_failed_operation' => true])->save();

            $order->session()->update(['has_failed_operations' => true]);

            OrderOperationFailed::dispatch($order, $operation->fresh(), $exception);

            return false;
        }
    }
}
