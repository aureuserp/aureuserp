<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class PriceRuleItemResource extends JsonResource
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
            'price_list_id'      => $this->price_list_id,
            'apply_to'           => $this->apply_to,
            'type'               => $this->type,
            'base'               => $this->base,
            'product_id'         => $this->product_id,
            'category_id'        => $this->category_id,
            'base_price_list_id' => $this->base_price_list_id,
            'min_quantity'       => (float) $this->min_quantity,
            'fixed_price'        => (float) $this->fixed_price,
            'percent_price'      => (float) $this->percent_price,
            'price_discount'     => (float) $this->price_discount,
            'price_markup'       => (float) $this->price_markup,
            'price_round'        => (float) $this->price_round,
            'price_surcharge'    => (float) $this->price_surcharge,
            'price_min_margin'   => (float) $this->price_min_margin,
            'price_max_margin'   => (float) $this->price_max_margin,
            'starts_at'          => $this->starts_at,
            'ends_at'            => $this->ends_at,
            'currency_id'        => $this->currency_id,
            'company_id'         => $this->company_id,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'product'            => ProductResource::make($this->whenLoaded('product')),
            'category'           => CategoryResource::make($this->whenLoaded('category')),
            'currency'           => CurrencyResource::make($this->whenLoaded('currency')),
        ];
    }
}
