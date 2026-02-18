<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class EmployeeResume extends Model
{
    protected $table = 'employees_employee_resumes';

    protected $fillable = [
        'employee_id',
        'employee_resume_line_type_id',
        'creator_id',
        'user_id',
        'display_type',
        'start_date',
        'end_date',
        'name',
        'description',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function resumeType()
    {
        return $this->belongsTo(EmployeeResumeLineType::class, 'employee_resume_line_type_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employeeResume) {
            $employeeResume->creator_id ??= Auth::id();
        });
    }
}
