<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class ActivityPlanTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'sort'              => $this->sort,
            'delay_count'       => $this->delay_count,
            'delay_unit'        => $this->delay_unit,
            'delay_from'        => $this->delay_from,
            'summary'           => $this->summary,
            'responsible_type'  => $this->responsible_type,
            'note'              => $this->note,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'activity_plan'     => ActivityPlanResource::make($this->whenLoaded('activityPlan')),
            'activity_type'     => ActivityTypeResource::make($this->whenLoaded('activityType')),
            'responsible'       => UserResource::make($this->whenLoaded('responsible')),
            'creator'           => UserResource::make($this->whenLoaded('createdBy')),
            'assigned_user'     => UserResource::make($this->whenLoaded('assignedUser')),
        ];
    }
}
