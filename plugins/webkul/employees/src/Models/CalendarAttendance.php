<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Database\Factories\CalendarAttendanceFactory;
use Webkul\Security\Models\User;

class CalendarAttendance extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'employees_calendar_attendances';

    protected $fillable = [
        'sort',
        'name',
        'day_of_week',
        'day_period',
        'week_type',
        'display_type',
        'date_from',
        'date_to',
        'hour_from',
        'hour_to',
        'duration_days',
        'calendar_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($calendarAttendance) {
            $calendarAttendance->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): CalendarAttendanceFactory
    {
        return CalendarAttendanceFactory::new();
    }
}
