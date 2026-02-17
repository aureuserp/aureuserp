<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class ProductSupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'sort'         => $this->sort,
            'delay'        => $this->delay,
            'product_name' => $this->product_name,
            'product_code' => $this->product_code,
            'starts_at'    => $this->starts_at,
            'ends_at'      => $this->ends_at,
            'min_qty'      => $this->min_qty,
            'price'        => $this->price,
            'discount'     => $this->discount,
            'product_id'   => $this->product_id,
            'partner_id'   => $this->partner_id,
            'currency_id'  => $this->currency_id,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'product'      => ProductResource::make($this->whenLoaded('product')),
            'partner'      => PartnerResource::make($this->whenLoaded('partner')),
            'currency'     => CurrencyResource::make($this->whenLoaded('currency')),
            'creator'      => UserResource::make($this->whenLoaded('creator')),
            'company'      => CompanyResource::make($this->whenLoaded('company')),
        ];
    }
}
