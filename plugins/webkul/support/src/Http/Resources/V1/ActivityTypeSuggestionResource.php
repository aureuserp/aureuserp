<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityTypeSuggestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'activity_type'              => ActivityTypeResource::make($this->whenLoaded('activityType')),
            'suggested_activity_type'    => ActivityTypeResource::make($this->whenLoaded('suggestedActivityType')),
        ];
    }
}
