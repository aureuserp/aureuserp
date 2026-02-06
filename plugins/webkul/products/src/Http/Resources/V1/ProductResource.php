<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'type'                 => $this->type?->value,
            'name'                 => $this->name,
            'service_tracking'     => $this->service_tracking,
            'reference'            => $this->reference,
            'barcode'              => $this->barcode,
            'price'                => (float) $this->price,
            'cost'                 => (float) $this->cost,
            'volume'               => (float) $this->volume,
            'weight'               => (float) $this->weight,
            'description'          => $this->description,
            'description_purchase' => $this->description_purchase,
            'description_sale'     => $this->description_sale,
            'enable_sales'         => (bool) $this->enable_sales,
            'enable_purchase'      => (bool) $this->enable_purchase,
            'is_favorite'          => (bool) $this->is_favorite,
            'is_configurable'      => (bool) $this->is_configurable,
            'images'               => $this->images,
            'sort'                 => $this->sort,
            'parent_id'            => $this->parent_id,
            'uom_id'               => $this->uom_id,
            'uom_po_id'            => $this->uom_po_id,
            'category_id'          => $this->category_id,
            'creator_id'           => $this->creator_id,
            'company_id'           => $this->company_id,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'deleted_at'           => $this->deleted_at,
            'parent'               => new ProductResource($this->whenLoaded('parent')),
            'uom'                  => new UOMResource($this->whenLoaded('uom')),
            'uom_po'               => new UOMResource($this->whenLoaded('uomPO')),
            'category'             => new CategoryResource($this->whenLoaded('category')),
            'creator'              => new UserResource($this->whenLoaded('creator')),
            'company'              => new CompanyResource($this->whenLoaded('company')),
            'tags'                 => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
