<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class PaymentMethodLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'sort'                 => $this->sort,
            'payment_method_id'    => $this->payment_method_id,
            'payment_account_id'   => $this->payment_account_id,
            'journal_id'           => $this->journal_id,
            'name'                 => $this->name,
            'creator_id'           => $this->creator_id,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'createdBy'            => new UserResource($this->whenLoaded('createdBy')),
            'paymentMethod'        => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'paymentAccount'       => new AccountResource($this->whenLoaded('paymentAccount')),
            'journal'              => new JournalResource($this->whenLoaded('journal')),
            'defaultAccount'       => new AccountResource($this->whenLoaded('defaultAccount')),
        ];
    }
}
