<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class UOMResource extends JsonResource
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
            'type'        => $this->type,
            'name'        => $this->name,
            'factor'      => $this->factor,
            'rounding'    => $this->rounding,
            'category_id' => $this->category_id,
            'creator_id'  => $this->creator_id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'deleted_at'  => $this->deleted_at,
            'category'    => UOMCategoryResource::make($this->whenLoaded('category')),
            'creator'     => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
