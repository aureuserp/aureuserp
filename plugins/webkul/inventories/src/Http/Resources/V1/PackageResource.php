<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'package_use'   => $this->package_use?->value,
            'pack_date'     => $this->pack_date,
            'package_type'  => PackageTypeResource::make($this->whenLoaded('packageType')),
            'location'      => LocationResource::make($this->whenLoaded('location')),
            'company'       => CompanyResource::make($this->whenLoaded('company')),
            'creator'       => UserResource::make($this->whenLoaded('creator')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
