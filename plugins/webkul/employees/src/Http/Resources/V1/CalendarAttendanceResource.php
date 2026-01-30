<?php

namespace Webkul\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'day_period'     => $this->day_period,
            'dayofweek'      => $this->dayofweek,
            'week_type'      => $this->week_type,
            'day_from'       => (float) $this->day_from,
            'day_to'         => (float) $this->day_to,
            'duration_days'  => (float) $this->duration_days,
            'duration_hours' => (float) $this->duration_hours,
            'hour_from'      => (float) $this->hour_from,
            'hour_to'        => (float) $this->hour_to,
            'sequence'       => $this->sequence,
            'display_type'   => $this->display_type,
            'calendar_id'    => $this->calendar_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'calendar'       => new CalendarResource($this->whenLoaded('calendar')),
        ];
    }
}
