<?php

namespace Webkul\Blog\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class TagResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'color'      => $this->color,
            'sort'       => $this->sort,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'creator'    => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
