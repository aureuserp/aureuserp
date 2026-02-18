<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Calendar;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;

class Leave extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'time_off_leaves';

    public function getModelTitle(): string
    {
        return __('time-off::models/leave.title');
    }

    protected $fillable = [
        'user_id',
        'manager_id',
        'holiday_status_id',
        'employee_id',
        'employee_company_id',
        'company_id',
        'department_id',
        'calendar_id',
        'meeting_id',
        'first_approver_id',
        'second_approver_id',
        'creator_id',
        'private_name',
        'attachment',
        'state',
        'duration_display',
        'request_date_from_period',
        'request_date_from',
        'request_date_to',
        'notes',
        'request_unit_half',
        'request_unit_hours',
        'date_from',
        'date_to',
        'number_of_days',
        'number_of_hours',
        'request_hour_from',
        'request_hour_to',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'user.name'                => __('time-off::models/leave.log-attributes.user'),
            'manger.name'              => __('time-off::models/leave.log-attributes.manager'),
            'holidayStatus.name'       => __('time-off::models/leave.log-attributes.holiday_status'),
            'employee.name'            => __('time-off::models/leave.log-attributes.employee'),
            'employeeCompany.name'     => __('time-off::models/leave.log-attributes.employee_company'),
            'department.name'          => __('time-off::models/leave.log-attributes.department'),
            'calendar.name'            => __('time-off::models/leave.log-attributes.calendar'),
            'firstApprover.name'       => __('time-off::models/leave.log-attributes.first_approver'),
            'lastApprover.name'        => __('time-off::models/leave.log-attributes.last_approver'),
            'private_name'             => __('time-off::models/leave.log-attributes.description'),
            'state'                    => __('time-off::models/leave.log-attributes.state'),
            'duration_display'         => __('time-off::models/leave.log-attributes.duration_display'),
            'request_date_from_period' => __('time-off::models/leave.log-attributes.request_date_from_period'),
            'request_date_from'        => __('time-off::models/leave.log-attributes.request_date_from'),
            'request_date_to'          => __('time-off::models/leave.log-attributes.request_date_to'),
            'notes'                    => __('time-off::models/leave.log-attributes.notes'),
            'request_unit_half'        => __('time-off::models/leave.log-attributes.request_unit_half'),
            'request_unit_hours'       => __('time-off::models/leave.log-attributes.request_unit_hours'),
            'date_from'                => __('time-off::models/leave.log-attributes.date_from'),
            'date_to'                  => __('time-off::models/leave.log-attributes.date_to'),
            'number_of_days'           => __('time-off::models/leave.log-attributes.number_of_days'),
            'number_of_hours'          => __('time-off::models/leave.log-attributes.number_of_hours'),
            'request_hour_from'        => __('time-off::models/leave.log-attributes.request_hour_from'),
            'request_hour_to'          => __('time-off::models/leave.log-attributes.request_hour_to'),
        ];
    }

    protected $casts = [
        'state'                    => State::class,
        'request_date_from_period' => RequestDateFromPeriod::class,
        'request_date_from'        => 'date',
        'date_from'                => 'date',
        'request_unit_half'        => 'boolean',
        'number_of_hours'          => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function holidayStatus(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'holiday_status_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employeeCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'employee_company_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id');
    }

    public function firstApprover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'first_approver_id');
    }

    public function secondApprover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'second_approver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            $authUser = Auth::user();

            $leave->creator_id = $authUser->id;

            $leave->company_id ??= $authUser?->default_company_id;
        });
    }
}
