<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\Session;

class SessionSalesDetailsReport
{
    /**
     * @return array{session: Session, orders: Collection, sales: array<int, array<string, mixed>>, refunds: array<int, array<string, mixed>>, sales_total: array{quantity: float, amount: float}, refunds_total: array{quantity: float, amount: float}, sales_taxes: array<int, array<string, mixed>>, refunds_taxes: array<int, array<string, mixed>>, payments: array<int, array<string, mixed>>, discounts: array{quantity: int, amount: float}, invoices: array<int, array<string, mixed>>, control: array<string, mixed>}
     */
    public function build(Session $session): array
    {
        $orders = $this->orders($session);

        $lines = $this->lines($orders->pluck('id'));

        $sales = $lines->filter(fn (OrderLine $line): bool => $line->qty > 0);

        $refunds = $lines->filter(fn (OrderLine $line): bool => $line->qty < 0);

        return [
            'session'       => $session,
            'orders'        => $orders,
            'sales'         => $this->groupByCategory($sales),
            'refunds'       => $this->groupByCategory($refunds),
            'sales_total'   => $this->totals($sales),
            'refunds_total' => $this->totals($refunds),
            'sales_taxes'   => $this->taxes($sales),
            'refunds_taxes' => $this->taxes($refunds),
            'payments'      => $this->payments($session, $orders->pluck('id')),
            'discounts'     => $this->discounts($lines),
            'invoices'      => $this->invoices($orders),
            'control'       => app(ClosingControlReport::class)->build($session),
        ];
    }

    protected function orders(Session $session): Collection
    {
        return Order::withoutGlobalScopes()
            ->where('session_id', $session->getKey())
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->with('accountMove')
            ->orderBy('id')
            ->get();
    }

    protected function lines(Collection $orderIds): Collection
    {
        return OrderLine::withoutGlobalScopes()
            ->whereIn('order_id', $orderIds)
            ->with(['product.category', 'taxes'])
            ->get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function groupByCategory(Collection $lines): array
    {
        return $lines
            ->groupBy(fn (OrderLine $line): string => $line->product?->category?->name ?? '')
            ->map(fn (Collection $group, string $category): array => [
                'name'     => $category,
                'quantity' => (float) $group->sum('qty'),
                'amount'   => (float) $group->sum('price_subtotal'),
                'products' => $group
                    ->groupBy(fn (OrderLine $line): string => $this->productName($line).'|'.(float) $line->discount)
                    ->map(fn (Collection $rows): array => [
                        'name'     => $this->productName($rows->first()),
                        'quantity' => (float) $rows->sum('qty'),
                        'amount'   => (float) $rows->sum('price_subtotal'),
                        'discount' => (float) $rows->first()->discount,
                    ])
                    ->sortBy('name')
                    ->values()
                    ->all(),
            ])
            ->sortBy('name')
            ->values()
            ->all();
    }

    protected function productName(OrderLine $line): string
    {
        return $line->full_product_name ?? $line->product?->name ?? '';
    }

    /**
     * @return array{quantity: float, amount: float}
     */
    protected function totals(Collection $lines): array
    {
        return [
            'quantity' => (float) $lines->sum('qty'),
            'amount'   => (float) $lines->sum('price_subtotal'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function taxes(Collection $lines): array
    {
        $taxes = [];

        foreach ($lines as $line) {
            foreach ($line->taxes as $tax) {
                $taxes[$tax->name] ??= ['name' => $tax->name, 'amount' => 0.0, 'base' => 0.0];

                $taxes[$tax->name]['amount'] += (float) $line->price_tax;
                $taxes[$tax->name]['base'] += (float) $line->price_subtotal;
            }
        }

        return array_values($taxes);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function payments(Session $session, Collection $orderIds): array
    {
        return Payment::withoutGlobalScopes()
            ->whereIn('order_id', $orderIds)
            ->with('paymentMethod')
            ->get()
            ->groupBy('payment_method_id')
            ->map(fn (Collection $group): array => [
                'name'   => trim(($group->first()->paymentMethod?->name ?? '').' '.$session->name),
                'amount' => (float) $group->sum('amount'),
            ])
            ->sortBy('name')
            ->values()
            ->all();
    }

    /**
     * @return array{quantity: int, amount: float}
     */
    protected function discounts(Collection $lines): array
    {
        $discounted = $lines->filter(fn (OrderLine $line): bool => ! float_is_zero((float) $line->discount, precisionDigits: 2));

        return [
            'quantity' => $discounted->count(),
            'amount'   => (float) $discounted->reduce(
                fn (float $total, OrderLine $line): float => $total
                    + ((float) $line->price_unit * (float) $line->qty * (float) $line->discount / 100),
                0.0,
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function invoices(Collection $orders): array
    {
        return $orders
            ->filter(fn (Order $order): bool => (bool) $order->accountMove)
            ->map(fn (Order $order): array => [
                'name'   => $order->accountMove->name,
                'order'  => $order->reference ?? $order->name,
                'amount' => (float) $order->accountMove->amount_total,
            ])
            ->values()
            ->all();
    }
}
