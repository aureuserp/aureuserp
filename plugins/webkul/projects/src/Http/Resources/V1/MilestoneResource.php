<?php

namespace Webkul\Project\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class MilestoneResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'deadline'     => $this->deadline,
            'is_completed' => (bool) $this->is_completed,
            'completed_at' => $this->completed_at,
            'project_id'   => $this->project_id,
            'creator_id'   => $this->creator_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'project'      => new ProjectResource($this->whenLoaded('project')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
