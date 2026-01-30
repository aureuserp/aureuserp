<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class CurrencyRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'rate'         => $this->rate,
            'inverse_rate' => $this->inverse_rate,
            'currency_id'  => $this->currency_id,
            'company_id'   => $this->company_id,
            'creator_id'   => $this->creator_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'currency'     => new CurrencyResource($this->whenLoaded('currency')),
            'company'      => new CompanyResource($this->whenLoaded('company')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
