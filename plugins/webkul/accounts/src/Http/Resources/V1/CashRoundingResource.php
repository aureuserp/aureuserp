<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class CashRoundingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'strategy'          => $this->strategy,
            'rounding_method'   => $this->rounding_method,
            'rounding'          => $this->rounding,
            'profit_account_id' => $this->profit_account_id,
            'loss_account_id'   => $this->loss_account_id,
            'creator_id'        => $this->creator_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'creator'           => UserResource::make($this->whenLoaded('creator')),
            'profitAccount'     => AccountResource::make($this->whenLoaded('profitAccount')),
            'lossAccount'       => AccountResource::make($this->whenLoaded('lossAccount')),
        ];
    }
}
