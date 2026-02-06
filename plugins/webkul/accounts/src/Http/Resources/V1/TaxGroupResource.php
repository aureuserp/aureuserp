<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CountryResource;

class TaxGroupResource extends JsonResource
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
            'name'               => $this->name,
            'preceding_subtotal' => $this->preceding_subtotal,
            'sort'               => $this->sort,
            'company_id'         => $this->company_id,
            'country_id'         => $this->country_id,
            'creator_id'         => $this->creator_id,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'company'            => CompanyResource::make($this->whenLoaded('company')),
            'country'            => CountryResource::make($this->whenLoaded('country')),
            'creator'            => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
