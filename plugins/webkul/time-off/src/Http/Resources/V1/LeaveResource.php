<?php

namespace Webkul\TimeOff\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Employee\Http\Resources\V1\CalendarResource;
use Webkul\Employee\Http\Resources\V1\DepartmentResource;
use Webkul\Employee\Http\Resources\V1\EmployeeResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class LeaveResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                       => $this->id,
            'private_name'             => $this->private_name,
            'attachment'               => $this->attachment,
            'state'                    => $this->state?->value,
            'duration_display'         => $this->duration_display,
            'request_date_from_period' => $this->request_date_from_period?->value,
            'request_date_from'        => $this->request_date_from,
            'request_date_to'          => $this->request_date_to,
            'notes'                    => $this->notes,
            'request_unit_half'        => (bool) $this->request_unit_half,
            'request_unit_hours'       => (bool) $this->request_unit_hours,
            'date_from'                => $this->date_from,
            'date_to'                  => $this->date_to,
            'number_of_days'           => (float) $this->number_of_days,
            'number_of_hours'          => (float) $this->number_of_hours,
            'request_hour_from'        => $this->request_hour_from,
            'request_hour_to'          => $this->request_hour_to,
            'user_id'                  => $this->user_id,
            'manager_id'               => $this->manager_id,
            'holiday_status_id'        => $this->holiday_status_id,
            'employee_id'              => $this->employee_id,
            'employee_company_id'      => $this->employee_company_id,
            'company_id'               => $this->company_id,
            'department_id'            => $this->department_id,
            'calendar_id'              => $this->calendar_id,
            'meeting_id'               => $this->meeting_id,
            'first_approver_id'        => $this->first_approver_id,
            'second_approver_id'       => $this->second_approver_id,
            'creator_id'               => $this->creator_id,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'user'                     => new UserResource($this->whenLoaded('user')),
            'manager'                  => new EmployeeResource($this->whenLoaded('manager')),
            'holidayStatus'            => new LeaveTypeResource($this->whenLoaded('holidayStatus')),
            'employee'                 => new EmployeeResource($this->whenLoaded('employee')),
            'employeeCompany'          => new CompanyResource($this->whenLoaded('employeeCompany')),
            'company'                  => new CompanyResource($this->whenLoaded('company')),
            'department'               => new DepartmentResource($this->whenLoaded('department')),
            'calendar'                 => new CalendarResource($this->whenLoaded('calendar')),
            'firstApprover'            => new EmployeeResource($this->whenLoaded('firstApprover')),
            'secondApprover'           => new EmployeeResource($this->whenLoaded('secondApprover')),
            'createdBy'                => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
