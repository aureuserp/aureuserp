<?php

namespace Webkul\TimeOff\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Employee\Http\Resources\V1\DepartmentResource;
use Webkul\Employee\Http\Resources\V1\EmployeeResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class LeaveAllocationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                                => $this->id,
            'name'                              => $this->name,
            'state'                             => $this->state,
            'allocation_type'                   => $this->allocation_type?->value,
            'date_from'                         => $this->date_from,
            'date_to'                           => $this->date_to,
            'last_executed_carryover_date'      => $this->last_executed_carryover_date,
            'last_called'                       => $this->last_called,
            'actual_last_called'                => $this->actual_last_called,
            'next_call'                         => $this->next_call,
            'carried_over_days_expiration_date' => $this->carried_over_days_expiration_date,
            'notes'                             => $this->notes,
            'already_accrued'                   => (float) $this->already_accrued,
            'number_of_days'                    => (float) $this->number_of_days,
            'number_of_hours_display'           => $this->number_of_hours_display,
            'yearly_accrued_amount'             => (float) $this->yearly_accrued_amount,
            'expiring_carryover_days'           => (float) $this->expiring_carryover_days,
            'holiday_status_id'                 => $this->holiday_status_id,
            'employee_id'                       => $this->employee_id,
            'employee_company_id'               => $this->employee_company_id,
            'manager_id'                        => $this->manager_id,
            'approver_id'                       => $this->approver_id,
            'second_approver_id'                => $this->second_approver_id,
            'department_id'                     => $this->department_id,
            'accrual_plan_id'                   => $this->accrual_plan_id,
            'creator_id'                        => $this->creator_id,
            'created_at'                        => $this->created_at,
            'updated_at'                        => $this->updated_at,
            'holidayStatus'                     => new LeaveTypeResource($this->whenLoaded('holidayStatus')),
            'employee'                          => new EmployeeResource($this->whenLoaded('employee')),
            'employeeCompany'                   => new CompanyResource($this->whenLoaded('employeeCompany')),
            'manager'                           => new UserResource($this->whenLoaded('manager')),
            'approver'                          => new UserResource($this->whenLoaded('approver')),
            'secondApprover'                    => new UserResource($this->whenLoaded('secondApprover')),
            'department'                        => new DepartmentResource($this->whenLoaded('department')),
            'accrualPlan'                       => new LeaveAccrualPlanResource($this->whenLoaded('accrualPlan')),
            'createdBy'                         => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
