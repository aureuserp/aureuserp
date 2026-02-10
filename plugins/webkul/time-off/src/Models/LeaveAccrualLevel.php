<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class LeaveAccrualLevel extends Model implements Sortable
{
    use HasFactory, SortableTrait;

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

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function accrualPlan()
    {
        return $this->belongsTo(LeaveAccrualPlan::class, 'accrual_plan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveAccrualLevel) {
            $leaveAccrualLevel->creator_id = Auth::id();
        });
    }
}
