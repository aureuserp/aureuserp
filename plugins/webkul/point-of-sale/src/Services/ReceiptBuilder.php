<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\Storage;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;

class ReceiptBuilder
{
    public function __construct(
        protected OrderCalculator $calculator,
    ) {}

    public function build(Order $order): array
    {
        $order->loadMissing([
            'lines.product',
            'lines.uom',
            'payments.paymentMethod',
            'partner',
            'currency',
            'user',
            'config.company',
        ]);

        $company = $order->config?->company;

        $currency = $order->currency?->name ?? $company?->currency?->name;

        return [
            'company' => [
                'name'    => $company?->name,
                'logo'    => $company?->logo ? Storage::url($company->logo) : null,
                'phone'   => $company?->phone,
                'email'   => $company?->email,
                'website' => $company?->website,
            ],
            'header'          => $order->config?->receipt_header,
            'footer'          => $order->config?->receipt_footer,
            'cashier'         => $order->user?->name,
            'customer'        => $order->partner?->name,
            'tracking_number' => $order->tracking_number,
            'reference'       => $order->reference,
            'name'            => $order->name,
            'ordered_at'      => $order->ordered_at,
            'currency'        => $currency,
            'lines'           => $order->lines
                ->map(fn (OrderLine $line): array => [
                    'name'       => $line->full_product_name ?? $line->product?->name,
                    'qty'        => (float) $line->qty,
                    'price_unit' => (float) $line->price_unit,
                    'discount'   => (float) $line->discount,
                    'uom'        => $line->uom?->name,
                    'note'       => $line->customer_note,
                    'total'      => (float) $line->price_subtotal_incl,
                ])
                ->all(),
            'subtotal' => (float) $order->amount_untaxed,
            'taxes'    => $this->calculator->taxBreakdown($order),
            'total'    => (float) $order->amount_total,
            'rounding' => (float) $order->amount_rounding,
            'payments' => $order->payments
                ->reject(fn (Payment $payment): bool => (bool) $payment->is_change)
                ->map(fn (Payment $payment): array => [
                    'name'   => $payment->paymentMethod?->name,
                    'amount' => (float) $payment->amount,
                ])
                ->values()
                ->all(),
            'change' => (float) $order->amount_return,
        ];
    }
}
