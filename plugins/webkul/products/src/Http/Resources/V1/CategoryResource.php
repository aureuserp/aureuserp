<?php

namespace Webkul\Product\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'full_name'   => $this->full_name,
            'parent_path' => $this->parent_path,
            'parent_id'   => $this->parent_id,
            'creator_id'  => $this->creator_id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'parent'      => new CategoryResource($this->whenLoaded('parent')),
            'creator'     => new UserResource($this->whenLoaded('creator')),
            'children'    => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
