<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id,
            'name'                        => $this->name,
            'full_name'                   => $this->full_name,
            'description'                 => $this->description,
            'parent_path'                 => $this->parent_path,
            'barcode'                     => $this->barcode,
            'type'                        => $this->type?->value,
            'position_x'                  => $this->position_x,
            'position_y'                  => $this->position_y,
            'position_z'                  => $this->position_z,
            'removal_strategy'            => $this->removal_strategy?->value,
            'cyclic_inventory_frequency'  => $this->cyclic_inventory_frequency,
            'last_inventory_date'         => $this->last_inventory_date,
            'next_inventory_date'         => $this->next_inventory_date,
            'is_scrap'                    => (bool) $this->is_scrap,
            'is_replenish'                => (bool) $this->is_replenish,
            'is_dock'                     => (bool) $this->is_dock,
            'parent'                      => LocationResource::make($this->whenLoaded('parent')),
            'children'                    => LocationResource::collection($this->whenLoaded('children')),
            'storage_category'            => StorageCategoryResource::make($this->whenLoaded('storageCategory')),
            'warehouse'                   => WarehouseResource::make($this->whenLoaded('warehouse')),
            'company'                     => CompanyResource::make($this->whenLoaded('company')),
            'creator'                     => UserResource::make($this->whenLoaded('creator')),
            'created_at'                  => $this->created_at,
            'updated_at'                  => $this->updated_at,
            'deleted_at'                  => $this->deleted_at,
        ];
    }
}
