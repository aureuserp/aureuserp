<?php

namespace Webkul\Project\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'color' => $this->color,
            'priority' => $this->priority,
            'state' => $this->state?->value,
            'sort' => $this->sort,
            'is_active' => (bool) $this->is_active,
            'is_recurring' => (bool) $this->is_recurring,
            'deadline' => $this->deadline,
            'working_hours_open' => (float) $this->working_hours_open,
            'working_hours_close' => (float) $this->working_hours_close,
            'allocated_hours' => (float) $this->allocated_hours,
            'remaining_hours' => (float) $this->remaining_hours,
            'effective_hours' => (float) $this->effective_hours,
            'total_hours_spent' => (float) $this->total_hours_spent,
            'subtask_effective_hours' => (float) $this->subtask_effective_hours,
            'overtime' => (float) $this->overtime,
            'progress' => (float) $this->progress,
            'stage_id' => $this->stage_id,
            'project_id' => $this->project_id,
            'partner_id' => $this->partner_id,
            'parent_id' => $this->parent_id,
            'company_id' => $this->company_id,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'stage' => new TaskStageResource($this->whenLoaded('stage')),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'partner' => new PartnerResource($this->whenLoaded('partner')),
            'parent' => new TaskResource($this->whenLoaded('parent')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'subTasks' => TaskResource::collection($this->whenLoaded('subTasks')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
