<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class LotResource extends JsonResource
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
            'name'            => $this->name,
            'description'     => $this->description,
            'reference'       => $this->reference,
            'properties'      => $this->properties,
            'expiry_reminded' => (bool) $this->expiry_reminded,
            'expiration_date' => $this->expiration_date,
            'use_date'        => $this->use_date,
            'removal_date'    => $this->removal_date,
            'alert_date'      => $this->alert_date,
            'product'         => ProductResource::make($this->whenLoaded('product')),
            'uom'             => UOMResource::make($this->whenLoaded('uom')),
            'location'        => LocationResource::make($this->whenLoaded('location')),
            'company'         => CompanyResource::make($this->whenLoaded('company')),
            'creator'         => UserResource::make($this->whenLoaded('creator')),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
