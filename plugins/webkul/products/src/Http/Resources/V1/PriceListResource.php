<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class PriceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'sort'        => $this->sort,
            'is_active'   => $this->is_active,
            'currency_id' => $this->currency_id,
            'creator_id'  => $this->creator_id,
            'company_id'  => $this->company_id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'currency'    => CurrencyResource::make($this->whenLoaded('currency')),
            'creator'     => UserResource::make($this->whenLoaded('creator')),
            'company'     => CompanyResource::make($this->whenLoaded('company')),
        ];
    }
}
