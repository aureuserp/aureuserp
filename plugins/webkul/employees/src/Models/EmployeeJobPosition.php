<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Database\Factories\EmployeeJobPositionFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class EmployeeJobPosition extends Model implements Sortable
{
    use HasCustomFields, HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'employees_job_positions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sort',
        'expected_employees',
        'no_of_employee',
        'no_of_recruitment',
        'department_id',
        'company_id',
        'creator_id',
        'employment_type_id',
        'recruiter_id',
        'name',
        'description',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'job_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    protected static function newFactory(): EmployeeJobPositionFactory
    {
        return EmployeeJobPositionFactory::new();
    }
}
