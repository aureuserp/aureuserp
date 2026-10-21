<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Account\Facades\Tax;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;

class OrderCalculator
{
    public function __construct(
        protected FiscalPositionResolver $fiscalPositions,
    ) {}

    public function recompute(Order $order): Order
    {
        $order->loadMissing(['lines.product', 'lines.taxes', 'payments.paymentMethod']);

        $order->lines->each(fn (OrderLine $line) => $this->recomputeLine($line));

        $order->load('lines');

        $amountUntaxed = (float) $order->lines->sum('price_subtotal');

        $amountTax = (float) $order->lines->sum('price_tax');

        $totalCost = (float) $order->lines->sum('total_cost');

        $amountTotal = float_round($amountUntaxed + $amountTax, precisionDigits: 4);

        $rounding = $this->roundingDifference($order, $amountTotal);

        $amountTotal = float_round($amountTotal + $rounding, precisionDigits: 4);

        $amountPaid = $order->settledAmount();

        $order->forceFill([
            'amount_untaxed'    => $amountUntaxed,
            'amount_tax'        => $amountTax,
            'amount_total'      => $amountTotal,
            'amount_paid'       => $amountPaid,
            'amount_difference' => float_round($amountPaid - $amountTotal, precisionDigits: 4),
            'amount_rounding'   => $rounding,
            'total_cost'        => $totalCost,
            'margin'            => float_round($amountUntaxed - $totalCost, precisionDigits: 4),
            'margin_percent'    => float_compare($amountUntaxed, 0, precisionDigits: 4) == 0
                ? 0
                : float_round(($amountUntaxed - $totalCost) / $amountUntaxed, precisionDigits: 4),
            'is_cost_computed'  => true,
        ])->save();

        return $order->refresh();
    }

    /**
     * @return array<int, array{id: int, name: string, amount: float, base: float}>
     */
    public function taxBreakdown(Order $order): array
    {
        $order->loadMissing(['lines.product', 'lines.taxes', 'fiscalPosition', 'partner', 'currency']);

        $groups = [];

        foreach ($order->lines as $line) {
            $taxes = $this->fiscalPositions->mapTaxes($order->fiscalPosition, $line->taxes);

            if ($taxes->isEmpty()) {
                continue;
            }

            $priceUnit = float_compare((float) $line->discount, 0, precisionDigits: 2) > 0
                ? (float) $line->price_unit * (1 - ((float) $line->discount / 100))
                : (float) $line->price_unit;

            $result = Tax::computeAll(
                $taxes,
                $priceUnit,
                $order->currency,
                (float) $line->qty,
                $line->product,
                $order->partner,
            );

            $counted = [];

            foreach ($result['taxes'] as $entry) {
                $key = $entry['id'];

                $groups[$key] ??= ['id' => $entry['id'], 'name' => $entry['name'], 'amount' => 0.0, 'base' => 0.0];
                $groups[$key]['amount'] += (float) $entry['amount'];

                if (! isset($counted[$key])) {
                    $groups[$key]['base'] += (float) $entry['base'];

                    $counted[$key] = true;
                }
            }
        }

        return collect($groups)
            ->map(fn (array $group): array => [
                ...$group,
                'amount' => float_round($group['amount'], precisionDigits: 4),
                'base'   => float_round($group['base'], precisionDigits: 4),
            ])
            ->values()
            ->all();
    }

    public function roundingDifference(Order $order, float $amountTotal): float
    {
        $config = $order->config;

        if (! $config?->enable_cash_rounding || ! $config->cashRounding) {
            return 0.0;
        }

        if ($config->enable_only_round_cash_method && ! $this->hasCashPayment($order)) {
            return 0.0;
        }

        $currency = $order->currency ?? $config->company?->currency;

        if (! $currency) {
            return 0.0;
        }

        return $config->cashRounding->computeDifference($currency, $amountTotal);
    }

    protected function hasCashPayment(Order $order): bool
    {
        return $order->payments
            ->reject(fn (Payment $payment): bool => (bool) $payment->is_change)
            ->contains(fn (Payment $payment): bool => (bool) $payment->paymentMethod?->is_cash_count);
    }

    public function recomputeLine(OrderLine $line): OrderLine
    {
        $line->loadMissing(['order.fiscalPosition', 'product', 'taxes']);

        $this->snapshotCost($line);

        $priceUnit = (float) $line->price_unit;

        $priceUnit = float_compare((float) $line->discount, 0, precisionDigits: 2) > 0
            ? $priceUnit * (1 - ((float) $line->discount / 100))
            : $priceUnit;

        $taxes = $this->fiscalPositions->mapTaxes($line->order?->fiscalPosition, $line->taxes);

        if ($taxes->isEmpty()) {
            $subtotal = float_round($priceUnit * (float) $line->qty, precisionDigits: 4);

            $line->price_subtotal = $subtotal;
            $line->price_tax = 0;
            $line->price_subtotal_incl = $subtotal;

            $this->applyMargin($line);

            $line->save();

            return $line;
        }

        $taxResult = Tax::computeAll(
            $taxes,
            $priceUnit,
            $line->order?->currency,
            (float) $line->qty,
            $line->product,
            $line->order?->partner,
        );

        $line->price_subtotal = float_round($taxResult['total_excluded'], precisionDigits: 4);
        $line->price_tax = float_round($taxResult['total_included'] - $taxResult['total_excluded'], precisionDigits: 4);
        $line->price_subtotal_incl = float_round($taxResult['total_included'], precisionDigits: 4);

        $this->applyMargin($line);

        $line->save();

        return $line;
    }

    public function applyMargin(OrderLine $line): OrderLine
    {
        $subtotal = (float) $line->price_subtotal;

        $line->margin = float_round($subtotal - (float) $line->total_cost, precisionDigits: 4);

        $line->margin_percent = float_compare($subtotal, 0, precisionDigits: 4) == 0
            ? 0
            : float_round((float) $line->margin / $subtotal, precisionDigits: 4);

        return $line;
    }

    public function snapshotCost(OrderLine $line): OrderLine
    {
        if (! $line->is_cost_computed) {
            $line->unit_cost = (float) ($line->product?->cost ?? 0);
        }

        $line->total_cost = float_round((float) $line->unit_cost * (float) $line->qty, precisionDigits: 4);

        $this->applyMargin($line);

        $line->is_cost_computed = true;

        return $line;
    }
}
