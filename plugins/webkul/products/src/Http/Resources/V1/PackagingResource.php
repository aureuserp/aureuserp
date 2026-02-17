<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class PackagingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'barcode'    => $this->barcode,
            'qty'        => $this->qty,
            'sort'       => $this->sort,
            'product_id' => $this->product_id,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product'    => ProductResource::make($this->whenLoaded('product')),
            'creator'    => UserResource::make($this->whenLoaded('creator')),
            'company'    => CompanyResource::make($this->whenLoaded('company')),
        ];
    }
}
