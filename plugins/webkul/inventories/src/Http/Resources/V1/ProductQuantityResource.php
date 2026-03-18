<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\ProductResource as BaseProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class ProductQuantityResource extends JsonResource
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
            'quantity'               => (float) $this->quantity,
            'reserved_quantity'      => (float) $this->reserved_quantity,
            'counted_quantity'       => $this->counted_quantity !== null ? (float) $this->counted_quantity : null,
            'difference_quantity'    => $this->difference_quantity !== null ? (float) $this->difference_quantity : null,
            'inventory_diff_quantity'=> $this->inventory_diff_quantity !== null ? (float) $this->inventory_diff_quantity : null,
            'available_quantity'     => (float) $this->available_quantity,
            'inventory_quantity_set' => (bool) $this->inventory_quantity_set,
            'scheduled_at'           => $this->scheduled_at,
            'incoming_at'            => $this->incoming_at,
            'product'                => BaseProductResource::make($this->whenLoaded('product')),
            'location'               => LocationResource::make($this->whenLoaded('location')),
            'storage_category'       => StorageCategoryResource::make($this->whenLoaded('storageCategory')),
            'lot'                    => LotResource::make($this->whenLoaded('lot')),
            'package'                => PackageResource::make($this->whenLoaded('package')),
            'partner'                => PartnerResource::make($this->whenLoaded('partner')),
            'user'                   => UserResource::make($this->whenLoaded('user')),
            'company'                => CompanyResource::make($this->whenLoaded('company')),
            'creator'                => UserResource::make($this->whenLoaded('creator')),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
