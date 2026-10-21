<?php

namespace Webkul\PointOfSale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'state'                 => $this->state,
            'config_id'             => $this->config_id,
            'stock_update_mode'     => $this->stock_update_mode,
            'started_at'            => $this->started_at,
            'stopped_at'            => $this->stopped_at,
            'cash_balance_start'    => $this->cash_balance_start,
            'cash_balance_end'      => $this->cash_balance_end,
            'cash_difference'       => $this->cash_difference,
            'order_count'           => $this->order_count,
            'total_payments_amount' => $this->total_payments_amount,
            'has_failed_operations' => $this->has_failed_operations,
            'is_rescue'             => $this->is_rescue,
        ];
    }
}
