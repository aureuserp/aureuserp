<?php

namespace Webkul\PointOfSale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;

class OrderHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'uuid'                 => $this->uuid,
            'name'                 => $this->name,
            'reference'            => $this->reference,
            'receipt_code'         => $this->receipt_code,
            'tracking_number'      => $this->tracking_number,
            'state'                => $this->state,
            'fiscal_position_id'   => $this->fiscal_position_id,
            'ordered_at'           => $this->ordered_at,
            'amount_untaxed'       => $this->amount_untaxed,
            'amount_tax'           => $this->amount_tax,
            'amount_total'         => $this->amount_total,
            'amount_paid'          => $this->amount_paid,
            'amount_return'        => $this->amount_return,
            'is_invoiced'          => $this->is_invoiced,
            'has_failed_operation' => $this->has_failed_operation,
            'cashier'              => $this->whenLoaded('user', fn (): ?string => $this->user?->name),
            'partner'              => $this->whenLoaded('partner', fn (): ?array => $this->partner ? [
                'id'   => $this->partner->id,
                'name' => $this->partner->name,
            ] : null),
            'table'                => $this->whenLoaded('table', fn (): ?array => $this->table ? [
                'id'           => $this->table->id,
                'table_number' => $this->table->table_number,
                'floor'        => $this->table->floor?->name,
            ] : null),
            'currency'             => $this->whenLoaded('currency', fn (): ?array => $this->currency ? [
                'id'   => $this->currency->id,
                'name' => $this->currency->name,
            ] : null),
            'lines'                => $this->whenLoaded('lines', fn (): array => $this->lines->map(fn (OrderLine $line): array => [
                'id'                   => $line->id,
                'uuid'                 => $line->uuid,
                'product_id'           => $line->product_id,
                'uom_id'               => $line->uom_id,
                'full_product_name'    => $line->full_product_name,
                'qty'                  => $line->qty,
                'price_unit'           => $line->price_unit,
                'discount'             => $line->discount,
                'price_subtotal'       => $line->price_subtotal,
                'price_subtotal_incl'  => $line->price_subtotal_incl,
                'tax_ids'              => $line->taxes->pluck('id')->values()->all(),
            ])->all()),
            'payments'             => $this->whenLoaded('payments', fn (): array => $this->payments->map(fn (Payment $payment): array => [
                'id'                => $payment->id,
                'uuid'              => $payment->uuid,
                'payment_method_id' => $payment->payment_method_id,
                'payment_method'    => $payment->paymentMethod?->name,
                'amount'            => $payment->amount,
                'is_change'         => $payment->is_change,
            ])->all()),
        ];
    }
}
