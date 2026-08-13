<?php

namespace Webkul\Support\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\CalendarAttendanceFactory;

class CalendarAttendance extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'calendar_attendances';

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
        'resource_type',
        'resource_id',
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

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getWeekType(\DateTime|Carbon $date): int
    {
        return (int) floor(($date->toDateTime()->format('z') + 1) / 7) % 2;
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
