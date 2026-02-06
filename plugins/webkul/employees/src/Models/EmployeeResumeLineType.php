<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Support\Facades\Auth;

class EmployeeResumeLineType extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'employees_employee_resume_line_types';

    protected $fillable = [
        'sort',
        'name',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function resume()
    {
        return $this->hasMany(EmployeeResume::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employeeResumeLineType) {
            $employeeResumeLineType->creator_id = Auth::id();
        });
    }
}
