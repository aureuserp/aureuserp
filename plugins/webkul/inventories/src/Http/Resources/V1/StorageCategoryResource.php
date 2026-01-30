<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class StorageCategoryResource extends JsonResource
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
            'sort'               => $this->sort,
            'allow_new_products' => $this->allow_new_products?->value,
            'parent_path'        => $this->parent_path,
            'max_weight'         => $this->max_weight,
            'company'            => CompanyResource::make($this->whenLoaded('company')),
            'creator'            => UserResource::make($this->whenLoaded('creator')),
            'locations'          => LocationResource::collection($this->whenLoaded('locations')),
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
