<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class AttendanceEvent extends Model
{
    use BelongsToCompany;

    public const DIRECTION_IN = 1;

    public const DIRECTION_OUT = -1;

    public const DIRECTION_UNKNOWN = 0;

    protected $table = 'attendance_events';

    protected $fillable = [
        'employee_id',
        'punched_at',
        'direction',
        'source',
        'source_label',
        'source_ref',
        'attendance_id',
        'company_id',
    ];

    protected $casts = [
        'punched_at' => 'datetime',
        'direction'  => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
