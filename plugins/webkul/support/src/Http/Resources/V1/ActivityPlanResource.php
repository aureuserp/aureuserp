<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class ActivityPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'plugin'                  => $this->plugin,
            'is_active'               => $this->is_active,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'deleted_at'              => $this->deleted_at,
            'company'                 => CompanyResource::make($this->whenLoaded('company')),
            'creator'                 => UserResource::make($this->whenLoaded('createdBy')),
            'activity_types'          => ActivityTypeResource::collection($this->whenLoaded('activityTypes')),
            'activity_plan_templates' => ActivityPlanTemplateResource::collection($this->whenLoaded('activityPlanTemplates')),
        ];
    }
}
