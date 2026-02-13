<?php

namespace Webkul\Purchase\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class RequisitionLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'qty'            => (float) $this->qty,
            'price_unit'     => (float) $this->price_unit,
            'requisition_id' => $this->requisition_id,
            'product_id'     => $this->product_id,
            'uom_id'         => $this->uom_id,
            'company_id'     => $this->company_id,
            'creator_id'     => $this->creator_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'requisition'    => new RequisitionResource($this->whenLoaded('requisition')),
            'product'        => new ProductResource($this->whenLoaded('product')),
            'uom'            => new UOMResource($this->whenLoaded('uom')),
            'company'        => new CompanyResource($this->whenLoaded('company')),
            'creator'        => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
