<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class RouteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                           => $this->id,
            'name'                         => $this->name,
            'sort'                         => $this->sort,
            'product_selectable'           => (bool) $this->product_selectable,
            'product_category_selectable'  => (bool) $this->product_category_selectable,
            'warehouse_selectable'         => (bool) $this->warehouse_selectable,
            'packaging_selectable'         => (bool) $this->packaging_selectable,
            'supplied_warehouse'           => WarehouseResource::make($this->whenLoaded('suppliedWarehouse')),
            'supplier_warehouse'           => WarehouseResource::make($this->whenLoaded('supplierWarehouse')),
            'warehouses'                   => WarehouseResource::collection($this->whenLoaded('warehouses')),
            'company'                      => CompanyResource::make($this->whenLoaded('company')),
            'creator'                      => UserResource::make($this->whenLoaded('creator')),
            'rules'                        => RuleResource::collection($this->whenLoaded('rules')),
            'created_at'                   => $this->created_at,
            'updated_at'                   => $this->updated_at,
            'deleted_at'                   => $this->deleted_at,
        ];
    }
}
