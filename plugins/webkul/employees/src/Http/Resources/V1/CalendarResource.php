<?php

namespace Webkul\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class CalendarResource extends JsonResource
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
            'name'                     => $this->name,
            'timezone'                 => $this->timezone,
            'hours_per_day'            => (float) $this->hours_per_day,
            'is_active'                => (bool) $this->is_active,
            'two_weeks_calendar'       => (bool) $this->two_weeks_calendar,
            'flexible_hours'           => (bool) $this->flexible_hours,
            'full_time_required_hours' => (float) $this->full_time_required_hours,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'deleted_at'               => $this->deleted_at,
            'creator'                  => new UserResource($this->whenLoaded('createdBy')),
            'company'                  => new CompanyResource($this->whenLoaded('company')),
            'attendance'               => CalendarAttendanceResource::collection($this->whenLoaded('attendance')),
        ];
    }
}
