<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class PackageTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->name,
            'sort'                   => $this->sort,
            'barcode'                => $this->barcode,
            'height'                 => $this->height,
            'width'                  => $this->width,
            'length'                 => $this->length,
            'base_weight'            => $this->base_weight,
            'max_weight'             => $this->max_weight,
            'shipper_package_code'   => $this->shipper_package_code,
            'package_carrier_type'   => $this->package_carrier_type,
            'company'                => CompanyResource::make($this->whenLoaded('company')),
            'creator'                => UserResource::make($this->whenLoaded('creator')),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
