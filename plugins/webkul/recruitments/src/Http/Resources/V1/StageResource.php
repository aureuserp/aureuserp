<?php

namespace Webkul\Recruitment\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class StageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'sort'           => $this->sort,
            'is_default'     => (bool) $this->is_default,
            'name'           => $this->name,
            'legend_blocked' => $this->legend_blocked,
            'legend_done'    => $this->legend_done,
            'legend_normal'  => $this->legend_normal,
            'requirements'   => $this->requirements,
            'fold'           => (bool) $this->fold,
            'hired_stage'    => (bool) $this->hired_stage,
            'creator_id'     => $this->creator_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'createdBy'      => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
