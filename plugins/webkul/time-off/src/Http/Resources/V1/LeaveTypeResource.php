<?php

namespace Webkul\TimeOff\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class LeaveTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                                  => $this->id,
            'sort'                                => $this->sort,
            'color'                               => $this->color,
            'max_allowed_negative'                => (float) $this->max_allowed_negative,
            'leave_validation_type'               => $this->leave_validation_type?->value,
            'requires_allocation'                 => (bool) $this->requires_allocation,
            'employee_requests'                   => (bool) $this->employee_requests,
            'allocation_validation_type'          => $this->allocation_validation_type,
            'time_type'                           => $this->time_type,
            'request_unit'                        => $this->request_unit,
            'name'                                => $this->name,
            'create_calendar_meeting'             => (bool) $this->create_calendar_meeting,
            'is_active'                           => (bool) $this->is_active,
            'show_on_dashboard'                   => (bool) $this->show_on_dashboard,
            'unpaid'                              => (bool) $this->unpaid,
            'include_public_holidays_in_duration' => (bool) $this->include_public_holidays_in_duration,
            'support_document'                    => (bool) $this->support_document,
            'allows_negative'                     => (bool) $this->allows_negative,
            'company_id'                          => $this->company_id,
            'creator_id'                          => $this->creator_id,
            'created_at'                          => $this->created_at,
            'updated_at'                          => $this->updated_at,
            'deleted_at'                          => $this->deleted_at,
            'company'                             => new CompanyResource($this->whenLoaded('company')),
            'createdBy'                           => new UserResource($this->whenLoaded('createdBy')),
            'notifiedTimeOffOfficers'             => UserResource::collection($this->whenLoaded('notifiedTimeOffOfficers')),
        ];
    }
}
