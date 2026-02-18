<?php

namespace Webkul\Purchase\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class VendorPriceListResource extends JsonResource
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
            'min_qty'      => (float) $this->min_qty,
            'price'        => (float) $this->price,
            'discount'     => (float) $this->discount,
            'product_id'   => $this->product_id,
            'partner_id'   => $this->partner_id,
            'currency_id'  => $this->currency_id,
            'company_id'   => $this->company_id,
            'creator_id'   => $this->creator_id,
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
            'product'      => new ProductResource($this->whenLoaded('product')),
            'partner'      => new PartnerResource($this->whenLoaded('partner')),
            'currency'     => new CurrencyResource($this->whenLoaded('currency')),
            'company'      => new CompanyResource($this->whenLoaded('company')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
