<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class CalendarLeaves extends Model
{
    use HasFactory;

    protected $table = 'employees_calendar_leaves';

    protected $fillable = [
        'name',
        'time_type',
        'date_from',
        'date_to',
        'company_id',
        'calendar_id',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($calendarLeave) {
            $authUser = Auth::user();

            $calendarLeave->creator_id ??= $authUser->id;

            $calendarLeave->company_id ??= $authUser?->default_company_id;
        });
    }
}
