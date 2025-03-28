<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class LeaveAccrualPlan extends Model
{
    use HasFactory;

    protected $table = 'time_off_leave_accrual_plans';

    protected $fillable = [
        'time_off_type_id',
        'company_id',
        'carryover_day',
        'creator_id',
        'name',
        'transition_mode',
        'accrued_gain_time',
        'carryover_date',
        'carryover_month',
        'added_value_type',
        'is_active',
        'is_based_on_worked_time',
    ];

    public function timeOffType()
    {
        return $this->belongsTo(LeaveType::class, 'time_off_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function leaveAccrualLevels()
    {
        return $this->hasMany(LeaveAccrualLevel::class, 'accrual_plan_id');
    }
}
