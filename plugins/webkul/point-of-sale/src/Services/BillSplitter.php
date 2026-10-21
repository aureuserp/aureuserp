<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;

class BillSplitter
{
    public function __construct(
        protected OrderCalculator $calculator,
    ) {}

    public function split(Order $order, array $lineQuantities): Order
    {
        return DB::transaction(function () use ($order, $lineQuantities): Order {
            if ($order->state !== OrderState::DRAFT) {
                throw new OrderAlreadyPaidException(
                    __('point-of-sale::system.order-workflow.split.not-draft', ['order' => $order->reference])
                );
            }

            $split = Order::create([
                'session_id'         => $order->session_id,
                'config_id'          => $order->config_id,
                'partner_id'         => $order->partner_id,
                'price_list_id'      => $order->price_list_id,
                'fiscal_position_id' => $order->fiscal_position_id,
                'currency_id'        => $order->currency_id,
                'table_id'           => $order->table_id,
                'origin'             => $order->reference,
            ]);

            foreach ($lineQuantities as $lineId => $quantity) {
                $quantity = abs((float) $quantity);

                if (float_is_zero($quantity, precisionDigits: 4)) {
                    continue;
                }

                $line = OrderLine::withoutGlobalScopes()
                    ->where('order_id', $order->id)
                    ->whereKey($lineId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $moved = min($quantity, abs((float) $line->qty));

                $this->copyLine($split, $line, $moved);

                $remaining = abs((float) $line->qty) - $moved;

                if (float_is_zero($remaining, precisionDigits: 4)) {
                    $line->delete();

                    continue;
                }

                $line->forceFill(['qty' => $remaining])->save();
            }

            $this->calculator->recompute($order->refresh());

            return $this->calculator->recompute($split->refresh());
        });
    }

    protected function copyLine(Order $split, OrderLine $line, float $quantity): OrderLine
    {
        $splitLine = OrderLine::create([
            'order_id'          => $split->id,
            'product_id'        => $line->product_id,
            'uom_id'            => $line->uom_id,
            'name'              => $line->name,
            'full_product_name' => $line->full_product_name,
            'qty'               => $quantity,
            'price_unit'        => $line->price_unit,
            'price_extra'       => $line->price_extra,
            'price_type'        => $line->price_type,
            'discount'          => $line->discount,
            'unit_cost'         => $line->unit_cost,
            'is_cost_computed'  => (bool) $line->is_cost_computed,
            'customer_note'     => $line->customer_note,
        ]);

        $splitLine->taxes()->sync($line->taxes->pluck('id')->all());

        return $splitLine;
    }
}
