<?php

namespace Webkul\TimeOff\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class LeaveAccrualPlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'transition_mode'         => $this->transition_mode,
            'accrued_gain_time'       => $this->accrued_gain_time?->value,
            'carryover_date'          => $this->carryover_date?->value,
            'carryover_month'         => $this->carryover_month?->value,
            'carryover_day'           => $this->carryover_day?->value,
            'added_value_type'        => $this->added_value_type,
            'is_active'               => (bool) $this->is_active,
            'is_based_on_worked_time' => (bool) $this->is_based_on_worked_time,
            'time_off_type_id'        => $this->time_off_type_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'timeOffType'             => new LeaveTypeResource($this->whenLoaded('timeOffType')),
            'company'                 => new CompanyResource($this->whenLoaded('company')),
            'createdBy'               => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
