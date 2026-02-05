<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class AttributeOptionResource extends JsonResource
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
            'name'         => $this->name,
            'color'        => $this->color,
            'extra_price'  => $this->extra_price,
            'sort'         => $this->sort,
            'attribute_id' => $this->attribute_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'attribute'    => AttributeResource::make($this->whenLoaded('attribute')),
            'creator'      => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
