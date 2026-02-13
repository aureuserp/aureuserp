<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Enums\AllocationType;

class LeaveAllocation extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'time_off_leave_allocations';

    public function getModelTitle(): string
    {
        return __('time-off::models/leave-allocation.title');
    }

    protected $fillable = [
        'holiday_status_id',
        'employee_id',
        'employee_company_id',
        'manager_id',
        'approver_id',
        'second_approver_id',
        'department_id',
        'accrual_plan_id',
        'creator_id',
        'name',
        'state',
        'allocation_type',
        'date_from',
        'date_to',
        'last_executed_carryover_date',
        'last_called',
        'actual_last_called',
        'next_call',
        'carried_over_days_expiration_date',
        'notes',
        'already_accrued',
        'number_of_days',
        'number_of_hours_display',
        'yearly_accrued_amount',
        'expiring_carryover_days',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'holidayStatus.name'                => __('time-off::models/leave-allocation.log-attributes.time_off_type'),
            'employee.name'                     => __('time-off::models/leave-allocation.log-attributes.employee'),
            'employeeCompany.name'              => __('time-off::models/leave-allocation.log-attributes.employee_company'),
            'approver.name'                     => __('time-off::models/leave-allocation.log-attributes.approver'),
            'secondApprover.name'               => __('time-off::models/leave-allocation.log-attributes.second_approver'),
            'department.name'                   => __('time-off::models/leave-allocation.log-attributes.department'),
            'accrualPlan.name'                  => __('time-off::models/leave-allocation.log-attributes.accrual_plan'),
            'creator.name'                      => __('time-off::models/leave-allocation.log-attributes.created_by'),
            'name'                              => __('time-off::models/leave-allocation.log-attributes.name'),
            'state'                             => __('time-off::models/leave-allocation.log-attributes.state'),
            'allocation_type'                   => __('time-off::models/leave-allocation.log-attributes.allocation_type'),
            'date_from'                         => __('time-off::models/leave-allocation.log-attributes.date_from'),
            'date_to'                           => __('time-off::models/leave-allocation.log-attributes.date_to'),
            'last_executed_carryover_date'      => __('time-off::models/leave-allocation.log-attributes.last_executed_carryover_date'),
            'last_called'                       => __('time-off::models/leave-allocation.log-attributes.last_called'),
            'actual_last_called'                => __('time-off::models/leave-allocation.log-attributes.actual_last_called'),
            'next_call'                         => __('time-off::models/leave-allocation.log-attributes.next_call'),
            'carried_over_days_expiration_date' => __('time-off::models/leave-allocation.log-attributes.carried_over_days_expiration_date'),
            'notes'                             => __('time-off::models/leave-allocation.log-attributes.notes'),
            'already_accrued'                   => __('time-off::models/leave-allocation.log-attributes.already_accrued'),
            'number_of_days'                    => __('time-off::models/leave-allocation.log-attributes.number_of_days'),
            'number_of_hours_display'           => __('time-off::models/leave-allocation.log-attributes.number_of_hours_display'),
            'yearly_accrued_amount'             => __('time-off::models/leave-allocation.log-attributes.yearly_accrued_amount'),
            'expiring_carryover_days'           => __('time-off::models/leave-allocation.log-attributes.expiring_carryover_days'),
        ];
    }

    protected $casts = [
        'allocation_type' => AllocationType::class,
        'date_to'         => 'date',
        'date_from'       => 'date',
        'number_of_days'  => 'decimal:4',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'employee_company_id');
    }

    public function employeeCompany()
    {
        return $this->belongsTo(Company::class, 'employee_company_id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    public function secondApprover()
    {
        return $this->belongsTo(Employee::class, 'second_approver_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function accrualPlan()
    {
        return $this->belongsTo(LeaveAccrualPlan::class, 'accrual_plan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function holidayStatus()
    {
        return $this->belongsTo(LeaveType::class, 'holiday_status_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveAllocation) {
            $authUser = Auth::user();

            $leaveAllocation->creator_id = $authUser->id;

            $leaveAllocation->employee_company_id ??= $authUser?->default_company_id;
        });
    }
}
