<?php

namespace Webkul\Project\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class ProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'tasks_label'             => $this->tasks_label,
            'description'             => $this->description,
            'visibility'              => $this->visibility,
            'color'                   => $this->color,
            'sort'                    => $this->sort,
            'start_date'              => $this->start_date,
            'end_date'                => $this->end_date,
            'allocated_hours'         => (float) $this->allocated_hours,
            'allow_timesheets'        => (bool) $this->allow_timesheets,
            'allow_milestones'        => (bool) $this->allow_milestones,
            'allow_task_dependencies' => (bool) $this->allow_task_dependencies,
            'is_active'               => (bool) $this->is_active,
            'stage_id'                => $this->stage_id,
            'partner_id'              => $this->partner_id,
            'company_id'              => $this->company_id,
            'user_id'                 => $this->user_id,
            'creator_id'              => $this->creator_id,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'deleted_at'              => $this->deleted_at,
            'stage'                   => new ProjectStageResource($this->whenLoaded('stage')),
            'partner'                 => new PartnerResource($this->whenLoaded('partner')),
            'company'                 => new CompanyResource($this->whenLoaded('company')),
            'user'                    => new UserResource($this->whenLoaded('user')),
            'creator'                 => new UserResource($this->whenLoaded('creator')),
            'tasks'                   => TaskResource::collection($this->whenLoaded('tasks')),
            'taskStages'              => TaskStageResource::collection($this->whenLoaded('taskStages')),
            'milestones'              => MilestoneResource::collection($this->whenLoaded('milestones')),
        ];
    }
}
