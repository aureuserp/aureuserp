<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;

class GlobalDiscountApplier
{
    public function __construct(
        protected OrderCalculator $calculator,
    ) {}

    public function apply(Order $order, float $percentage): Order
    {
        return DB::transaction(function () use ($order, $percentage): Order {
            $config = $order->config;

            if (! $config?->enable_global_discount || ! $config->discount_product_id) {
                throw new PosConfigurationException(
                    __('point-of-sale::system.global-discount.not-enabled')
                );
            }

            if ($order->state !== OrderState::DRAFT) {
                throw new PosConfigurationException(
                    __('point-of-sale::system.global-discount.not-draft', ['order' => $order->reference])
                );
            }

            $percentage = max(0, min(100, $percentage));

            $this->removeExisting($order);

            $order = $this->calculator->recompute($order->refresh());

            $amount = float_round((float) $order->amount_untaxed * ($percentage / 100), precisionDigits: 4);

            if (float_is_zero($amount, precisionDigits: 4)) {
                return $order;
            }

            $this->discountLinesFor($order, $amount);

            return $this->calculator->recompute($order->refresh());
        });
    }

    public function remove(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $this->removeExisting($order);

            return $this->calculator->recompute($order->refresh());
        });
    }

    protected function removeExisting(Order $order): void
    {
        $order->lines()
            ->where('product_id', $order->config?->discount_product_id)
            ->get()
            ->each(fn (OrderLine $line) => $line->delete());
    }

    protected function discountLinesFor(Order $order, float $amount): void
    {
        $order->loadMissing('lines.taxes');

        $groups = $order->lines
            ->reject(fn (OrderLine $line): bool => $line->product_id === $order->config->discount_product_id)
            ->groupBy(fn (OrderLine $line): string => $line->taxes->pluck('id')->sort()->implode(','));

        $untaxed = (float) $order->lines->sum('price_subtotal');

        foreach ($groups as $taxKey => $lines) {
            $groupSubtotal = (float) $lines->sum('price_subtotal');

            if (float_is_zero($groupSubtotal, precisionDigits: 4)) {
                continue;
            }

            $share = float_compare($untaxed, 0, precisionDigits: 4) == 0
                ? 0.0
                : float_round($amount * ($groupSubtotal / $untaxed), precisionDigits: 4);

            if (float_is_zero($share, precisionDigits: 4)) {
                continue;
            }

            $line = OrderLine::create([
                'order_id'   => $order->id,
                'product_id' => $order->config->discount_product_id,
                'qty'        => 1,
                'price_unit' => -$share,
            ]);

            $line->taxes()->sync(array_filter(explode(',', (string) $taxKey)));
        }
    }
}
