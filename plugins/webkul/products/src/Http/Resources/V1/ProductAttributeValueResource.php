<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeValueResource extends JsonResource
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
            'extra_price'          => $this->extra_price,
            'product_id'           => $this->product_id,
            'attribute_id'         => $this->attribute_id,
            'product_attribute_id' => $this->product_attribute_id,
            'attribute_option_id'  => $this->attribute_option_id,
            'attribute'            => new AttributeResource($this->whenLoaded('attribute')),
            'product_attribute'    => new ProductAttributeResource($this->whenLoaded('productAttribute')),
            'attribute_option'     => new AttributeOptionResource($this->whenLoaded('attributeOption')),
        ];
    }
}
