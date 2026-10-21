<?php

namespace Webkul\PointOfSale\Services;

use Webkul\PointOfSale\Enums\CashMovementType;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Models\CashMovement;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Session;

class ClosingControlReport
{
    public function build(Session $session): array
    {
        $session->loadMissing(['config.paymentMethods']);

        $orders = Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->get();

        $payments = Payment::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('order_id', $orders->pluck('id'))
            ->get();

        $settled = $payments->where('is_change', false);

        $methods = $session->config->paymentMethods
            ->reject(fn (PaymentMethod $method): bool => $method->type === PaymentMethodType::PAY_LATER);

        $cashMethod = $methods->firstWhere('is_cash_count', true);

        $cashPayments = $cashMethod
            ? (float) $payments->where('payment_method_id', $cashMethod->id)->sum('amount')
            : 0.0;

        $orderTotals = (float) $orders->sum('amount_total');

        return [
            'orders_details' => [
                'quantity' => $orders->count(),
                'amount'   => $orderTotals,
            ],
            'opening_notes'        => $session->opening_notes,
            'default_cash_details' => $cashMethod ? [
                'id'             => $cashMethod->id,
                'name'           => $cashMethod->name,
                'opening'        => (float) $session->cash_balance_start,
                'payment_amount' => $cashPayments,
                'moves'          => $this->moves($session),
                'amount'         => (float) $session->expectedCashBalance(),
            ] : null,
            'non_cash_payment_methods' => $methods
                ->reject(fn (PaymentMethod $method): bool => (bool) $method->is_cash_count)
                ->map(fn (PaymentMethod $method): array => [
                    'id'     => $method->id,
                    'name'   => $method->name,
                    'type'   => $method->type,
                    'number' => $settled->where('payment_method_id', $method->id)->count(),
                    'amount' => (float) $settled->where('payment_method_id', $method->id)->sum('amount'),
                ])
                ->values()
                ->all(),
            'amount_authorized_diff' => $session->config->enable_maximum_difference
                ? (float) $session->config->amount_authorized_diff
                : null,
        ];
    }

    protected function moves(Session $session): array
    {
        $in = 0;

        $out = 0;

        return CashMovement::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->orderBy('id')
            ->get()
            ->map(function (CashMovement $movement) use (&$in, &$out): array {
                $isIn = $movement->type === CashMovementType::IN;

                $isIn ? $in++ : $out++;

                return [
                    'name' => $movement->reason ?: __(
                        'point-of-sale::filament/pos/pages/terminal.closing.'.($isIn ? 'cash-in' : 'cash-out'),
                        ['number' => $isIn ? $in : $out],
                    ),
                    'amount' => $isIn
                        ? (float) $movement->amount
                        : -1 * (float) $movement->amount,
                ];
            })
            ->all();
    }
}
