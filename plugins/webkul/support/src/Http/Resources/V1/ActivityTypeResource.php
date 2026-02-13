<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class ActivityTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'sort'                     => $this->sort,
            'delay_count'              => $this->delay_count,
            'delay_unit'               => $this->delay_unit,
            'delay_from'               => $this->delay_from,
            'icon'                     => $this->icon,
            'decoration_type'          => $this->decoration_type,
            'chaining_type'            => $this->chaining_type,
            'plugin'                   => $this->plugin,
            'category'                 => $this->category,
            'name'                     => $this->name,
            'summary'                  => $this->summary,
            'default_note'             => $this->default_note,
            'is_active'                => $this->is_active,
            'keep_done'                => $this->keep_done,
            'activity_plan_id'         => $this->activity_plan_id,
            'triggered_next_type_id'   => $this->triggered_next_type_id,
            'creator_id'               => $this->creator_id,
            'default_user_id'          => $this->default_user_id,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'deleted_at'               => $this->deleted_at,
            'activity_plan'            => new ActivityPlanResource($this->whenLoaded('activityPlan')),
            'triggered_next_type'      => new ActivityTypeResource($this->whenLoaded('triggeredNextType')),
            'activity_types'           => ActivityTypeResource::collection($this->whenLoaded('activityTypes')),
            'suggested_activity_types' => ActivityTypeResource::collection($this->whenLoaded('suggestedActivityTypes')),
            'creator'                  => new UserResource($this->whenLoaded('createdBy')),
            'default_user'             => new UserResource($this->whenLoaded('defaultUser')),
        ];
    }
}
