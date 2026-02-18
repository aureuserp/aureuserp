<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Enums\LeaveValidationType;

class LeaveType extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'time_off_leave_types';

    protected $fillable = [
        'sort',
        'color',
        'company_id',
        'max_allowed_negative',
        'creator_id',
        'leave_validation_type',
        'requires_allocation',
        'employee_requests',
        'allocation_validation_type',
        'time_type',
        'request_unit',
        'name',
        'create_calendar_meeting',
        'is_active',
        'show_on_dashboard',
        'unpaid',
        'include_public_holidays_in_duration',
        'support_document',
        'allows_negative',
    ];

    protected $casts = [
        'leave_validation_type' => LeaveValidationType::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function notifiedTimeOffOfficers()
    {
        return $this->belongsToMany(User::class, 'time_off_user_leave_types', 'leave_type_id', 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveType) {
            $authUser = Auth::user();

            $leaveType->creator_id = $authUser->id;

            $leaveType->company_id ??= $authUser?->default_company_id;
        });
    }
}
