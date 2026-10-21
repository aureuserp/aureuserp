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
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\StockUpdateMode;
use Webkul\PointOfSale\Events\OrderOperationFailed;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Session;
use Webkul\Product\Enums\ProductType;

class SessionPickingGenerator
{
    public function generateFor(Session $session): ?Operation
    {
        if ($session->stock_update_mode !== StockUpdateMode::AT_CLOSING) {
            return null;
        }

        $orders = Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->whereNull('operation_id')
            ->whereNull('shipped_at')
            ->with(['lines.product'])
            ->get();

        $lines = $orders
            ->flatMap(fn (Order $order) => $order->lines)
            ->filter(fn (OrderLine $line): bool => $line->product?->type === ProductType::GOODS
                && $line->product?->is_storable
                && ! float_is_zero((float) $line->qty, precisionDigits: 4));

        if ($lines->isEmpty()) {
            return null;
        }

        $quantities = $lines
            ->groupBy('product_id')
            ->map(fn (Collection $productLines): float => (float) $productLines->sum('qty'))
            ->reject(fn (float $quantity): bool => float_is_zero($quantity, precisionDigits: 4));

        if ($quantities->isEmpty()) {
            return null;
        }

        $primary = null;

        foreach ([false, true] as $isReturn) {
            $selection = $quantities->filter(fn (float $quantity): bool => $isReturn
                ? $quantity < 0
                : $quantity > 0);

            if ($selection->isEmpty()) {
                continue;
            }

            $operationType = $isReturn
                ? $session->config->returnOperationType
                : $session->config->operationType;

            if (! $operationType) {
                continue;
            }

            $operation = $this->buildOperation($session, $operationType, $lines, $selection);

            $this->forceComplete($session, $orders, $operation);

            $primary ??= $operation;
        }

        if (! $primary) {
            return null;
        }

        $orders->each(fn (Order $order) => $order->forceFill(['operation_id' => $primary->id])->save());

        return $primary->refresh();
    }

    protected function buildOperation(Session $session, OperationType $operationType, Collection $lines, Collection $quantities): Operation
    {
        $operation = Operation::create([
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $operationType->source_location_id,
            'destination_location_id' => $operationType->destination_location_id,
            'state'                   => OperationState::DRAFT,
            'move_type'               => MoveType::DIRECT,
            'scheduled_at'            => now(),
            'origin'                  => $session->name,
            'user_id'                 => $session->user_id,
            'company_id'              => $session->company_id,
        ]);

        $lines
            ->groupBy('product_id')
            ->only($quantities->keys()->all())
            ->each(function (Collection $productLines, $productId) use ($operation, $operationType, $quantities, $session): void {
                $quantity = (float) $quantities->get($productId);

                $line = $productLines->first();

                Move::create([
                    'name'                    => $line->full_product_name ?? $line->product?->name,
                    'state'                   => MoveState::DRAFT,
                    'product_id'              => $productId,
                    'uom_id'                  => $line->uom_id ?? $line->product?->uom_id,
                    'product_uom_qty'         => abs($quantity),
                    'operation_id'            => $operation->id,
                    'operation_type_id'       => $operationType->id,
                    'source_location_id'      => $operation->source_location_id,
                    'destination_location_id' => $operation->destination_location_id,
                    'scheduled_at'            => now(),
                    'company_id'              => $session->company_id,
                ]);
            });

        return $operation->refresh();
    }

    protected function forceComplete(Session $session, Collection $orders, Operation $operation): bool
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

            $session->forceFill(['has_failed_operations' => true])->save();

            $orders->each(fn (Order $order) => $order->forceFill(['has_failed_operation' => true])->save());

            OrderOperationFailed::dispatch($orders->first(), $operation->fresh(), $exception);

            return false;
        }
    }
}
