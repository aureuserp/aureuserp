<?php

namespace Webkul\Recruitment\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class RefuseReasonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'sort'       => $this->sort,
            'name'       => $this->name,
            'template'   => $this->template,
            'is_active'  => (bool) $this->is_active,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'createdBy'  => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
