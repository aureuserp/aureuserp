<?php

namespace Webkul\PointOfSale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'uuid'                 => $this->uuid,
            'name'                 => $this->name,
            'reference'            => $this->reference,
            'tracking_number'      => $this->tracking_number,
            'receipt_code'         => $this->receipt_code,
            'state'                => $this->state,
            'session_id'           => $this->session_id,
            'config_id'            => $this->config_id,
            'partner_id'           => $this->partner_id,
            'table_id'             => $this->table_id,
            'ordered_at'           => $this->ordered_at,
            'amount_untaxed'       => $this->amount_untaxed,
            'amount_tax'           => $this->amount_tax,
            'amount_total'         => $this->amount_total,
            'amount_paid'          => $this->amount_paid,
            'amount_return'        => $this->amount_return,
            'amount_rounding'      => $this->amount_rounding,
            'has_failed_operation' => $this->has_failed_operation,
            'is_invoiced'          => $this->is_invoiced,
        ];
    }
}
