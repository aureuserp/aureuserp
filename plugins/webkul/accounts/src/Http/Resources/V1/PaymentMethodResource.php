<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'code'               => $this->code,
            'payment_type'       => $this->payment_type,
            'name'               => $this->name,
            'creator_id'         => $this->creator_id,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'creator'            => new UserResource($this->whenLoaded('creator')),
            'accountMovePayment' => MoveResource::collection($this->whenLoaded('accountMovePayment')),
        ];
    }
}
