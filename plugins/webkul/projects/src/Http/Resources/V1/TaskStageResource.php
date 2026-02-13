<?php

namespace Webkul\Project\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class TaskStageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'is_active'    => (bool) $this->is_active,
            'is_collapsed' => (bool) $this->is_collapsed,
            'sort'         => $this->sort,
            'project_id'   => $this->project_id,
            'company_id'   => $this->company_id,
            'user_id'      => $this->user_id,
            'creator_id'   => $this->creator_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'deleted_at'   => $this->deleted_at,
            'project'      => new ProjectResource($this->whenLoaded('project')),
            'user'         => new UserResource($this->whenLoaded('user')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'company'      => new CompanyResource($this->whenLoaded('company')),
            'tasks'        => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
