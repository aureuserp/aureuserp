<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class LeaveAccrualLevel extends Model
{
    use HasFactory;

    protected $table = 'time_off_leave_accrual_levels';

    protected $fillable = [
        'sort',
        'accrual_plan_id',
        'start_count',
        'first_day',
        'second_day',
        'first_month_day',
        'second_month_day',
        'yearly_day',
        'postpone_max_days',
        'accrual_validity_count',
        'creator_id',
        'start_type',
        'added_value_type',
        'frequency',
        'week_day',
        'first_month',
        'second_month',
        'yearly_month',
        'action_with_unused_accruals',
        'accrual_validity_type',
        'added_value',
        'maximum_leave',
        'maximum_leave_yearly',
        'cap_accrued_time',
        'cap_accrued_time_yearly',
        'accrual_validity',
    ];

    public function accrualPlan()
    {
        return $this->belongsTo(LeaveAccrualPlan::class, 'accrual_plan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
