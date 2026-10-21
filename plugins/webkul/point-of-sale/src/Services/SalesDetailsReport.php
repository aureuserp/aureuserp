<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;

class SalesDetailsReport
{
    public function build(?string $startDate, ?string $endDate, ?int $configId = null): array
    {
        $orderIds = Order::query()
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->when($startDate, fn ($query) => $query->whereDate('ordered_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('ordered_at', '<=', $endDate))
            ->when($configId, fn ($query) => $query->where('config_id', $configId))
            ->pluck('id');

        return [
            'orders'   => $this->orderTotals($orderIds),
            'products' => $this->productLines($orderIds),
            'payments' => $this->paymentTotals($orderIds),
            'taxes'    => $this->taxTotals($orderIds),
        ];
    }

    protected function orderTotals($orderIds): array
    {
        $orders = Order::query()->whereIn('id', $orderIds)->get();

        return [
            'count'   => $orders->count(),
            'untaxed' => (float) $orders->sum('amount_untaxed'),
            'taxes'   => (float) $orders->sum('amount_tax'),
            'total'   => (float) $orders->sum('amount_total'),
        ];
    }

    protected function productLines($orderIds)
    {
        return OrderLine::query()
            ->whereIn('order_id', $orderIds)
            ->select('product_id')
            ->selectRaw('SUM(qty) as quantity')
            ->selectRaw('SUM(price_subtotal) as untaxed')
            ->selectRaw('SUM(price_subtotal_incl) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('product')
            ->get();
    }

    protected function paymentTotals($orderIds)
    {
        return Payment::query()
            ->whereIn('order_id', $orderIds)
            ->select('payment_method_id')
            ->selectRaw('SUM(amount) as amount')
            ->selectRaw('COUNT(*) as payment_count')
            ->groupBy('payment_method_id')
            ->with('paymentMethod')
            ->get();
    }

    protected function taxTotals($orderIds)
    {
        return DB::table('pos_order_line_taxes')
            ->join('pos_order_lines', 'pos_order_lines.id', '=', 'pos_order_line_taxes.order_line_id')
            ->join('accounts_taxes', 'accounts_taxes.id', '=', 'pos_order_line_taxes.tax_id')
            ->whereIn('pos_order_lines.order_id', $orderIds)
            ->select('accounts_taxes.name')
            ->selectRaw('SUM(pos_order_lines.price_subtotal) as base')
            ->selectRaw('SUM(pos_order_lines.price_tax) as amount')
            ->groupBy('accounts_taxes.name')
            ->get();
    }
}
