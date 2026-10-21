<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\OrderLineLot;

class LotResolver
{
    public function applyTo(Order $order, Move $move, OrderLine $line): void
    {
        $lots = $line->lots;

        if ($lots->isEmpty() || $line->product?->tracking === ProductTracking::QTY) {
            return;
        }

        $move->lines()->delete();

        $lots->each(function (OrderLineLot $orderLineLot) use ($order, $move, $line): void {
            $lot = $this->resolveLot($order, $line, $orderLineLot);

            $quantity = $line->product?->tracking === ProductTracking::SERIAL
                ? 1.0
                : (float) ($orderLineLot->qty ?: abs((float) $line->qty));

            MoveLine::create([
                'move_id'                 => $move->id,
                'product_id'              => $line->product_id,
                'uom_id'                  => $line->uom_id ?? $line->product?->uom_id,
                'lot_id'                  => $lot?->id,
                'lot_name'                => $orderLineLot->lot_name,
                'qty'                     => $quantity,
                'uom_qty'                 => $quantity,
                'source_location_id'      => $move->source_location_id,
                'destination_location_id' => $move->destination_location_id,
                'operation_id'            => $move->operation_id,
                'company_id'              => $move->company_id,
            ]);
        });
    }

    protected function resolveLot(Order $order, OrderLine $line, OrderLineLot $orderLineLot): ?Lot
    {
        if ($orderLineLot->lot_id) {
            return $orderLineLot->lot;
        }

        $lot = Lot::query()
            ->where('product_id', $line->product_id)
            ->where('name', $orderLineLot->lot_name)
            ->where(owned_by_company($order->company_id))
            ->first();

        if (! $lot && $order->config?->operationType?->use_create_lots) {
            $lot = Lot::create([
                'name'       => $orderLineLot->lot_name,
                'product_id' => $line->product_id,
                'uom_id'     => $line->uom_id ?? $line->product?->uom_id,
                'company_id' => $order->company_id,
            ]);
        }

        if ($lot) {
            $orderLineLot->forceFill(['lot_id' => $lot->id])->save();
        }

        return $lot;
    }
}
