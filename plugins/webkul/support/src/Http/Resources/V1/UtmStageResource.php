<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class UtmStageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'sort'       => $this->sort,
            'name'       => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator'    => UserResource::make($this->whenLoaded('createdBy')),
        ];
    }
}
