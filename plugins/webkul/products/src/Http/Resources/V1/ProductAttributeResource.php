<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class ProductAttributeResource extends JsonResource
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
            'product_id'   => $this->product_id,
            'attribute_id' => $this->attribute_id,
            'creator_id'   => $this->creator_id,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
            'attribute'    => new AttributeResource($this->whenLoaded('attribute')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'values'       => ProductAttributeValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
