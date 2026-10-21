<?php

namespace Webkul\PointOfSale\Services;

use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\Session;

class SessionReportBuilder
{
    public function build(int $sessionId): ?array
    {
        $session = Session::query()->with(['config', 'user', 'cashMovements'])->find($sessionId);

        if (! $session) {
            return null;
        }

        $orders = Order::query()
            ->where('session_id', $session->id)
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->get();

        $payments = Payment::query()
            ->where('session_id', $session->id)
            ->with('paymentMethod')
            ->get();

        return [
            'session'  => $session,
            'orders'   => [
                'count'   => $orders->count(),
                'untaxed' => (float) $orders->sum('amount_untaxed'),
                'taxes'   => (float) $orders->sum('amount_tax'),
                'total'   => (float) $orders->sum('amount_total'),
                'refunds' => $orders->where('amount_total', '<', 0)->count(),
            ],
            'payments' => $payments
                ->groupBy('payment_method_id')
                ->map(fn ($group): array => [
                    'name'   => $group->first()->paymentMethod?->name,
                    'count'  => $group->count(),
                    'amount' => (float) $group->sum('amount'),
                ])
                ->values(),
            'cash'     => [
                'opening'    => (float) $session->cash_balance_start,
                'movements'  => $session->cashMovementTotal(),
                'expected'   => $session->expectedCashBalance(),
                'counted'    => $session->cash_balance_end_real !== null ? (float) $session->cash_balance_end_real : null,
                'difference' => $session->cash_difference !== null ? (float) $session->cash_difference : null,
            ],
        ];
    }
}
