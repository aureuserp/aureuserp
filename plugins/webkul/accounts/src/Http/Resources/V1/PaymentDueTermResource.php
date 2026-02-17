<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class PaymentDueTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'value'           => $this->value,
            'value_amount'    => $this->value_amount,
            'delay_type'      => $this->delay_type,
            'nb_days'         => $this->nb_days,
            'days_next_month' => $this->days_next_month,
            'payment_id'      => $this->payment_id,
            'creator_id'      => $this->creator_id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'paymentTerm'     => PaymentTermResource::make($this->whenLoaded('paymentTerm')),
            'creator'         => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
