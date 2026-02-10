<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Database\Factories\CalendarFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Calendar extends Model
{
    use HasCustomFields, HasFactory, SoftDeletes;

    protected $table = 'employees_calendars';

    protected $fillable = [
        'name',
        'timezone',
        'hours_per_day',
        'is_active',
        'two_weeks_calendar',
        'flexible_hours',
        'full_time_required_hours',
        'creator_id',
        'company_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function attendance()
    {
        return $this->hasMany(CalendarAttendance::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($calendar) {
            $calendar->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): CalendarFactory
    {
        return CalendarFactory::new();
    }
}
