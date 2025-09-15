<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Database\Factories\DepartmentFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Department extends Model
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'employees_departments';

    protected $fillable = [
        'name',
        'manager_id',
        'company_id',
        'parent_id',
        'master_department_id',
        'complete_name',
        'parent_path',
        'creator_id',
        'color',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function masterDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'master_department_id');
    }

    public function jobPositions(): HasMany
    {
        return $this->hasMany(EmployeeJobPosition::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($department) {
            if (! static::validateNoRecursion($department)) {
                throw new InvalidArgumentException('Circular reference detected in department hierarchy');
            }

            static::handleDepartmentData($department);
        });

        static::updating(function ($department) {
            if (! static::validateNoRecursion($department)) {
                throw new InvalidArgumentException('Circular reference detected in department hierarchy');
            }

            static::handleDepartmentData($department);
        });
    }

    protected static function validateNoRecursion($department)
    {
        if (! $department->parent_id) {
            return true;
        }

        if ($department->exists && $department->id == $department->parent_id) {
            return false;
        }

        $visitedIds = [$department->exists ? $department->id : -1];
        $currentParentId = $department->parent_id;

        while ($currentParentId) {
            if (in_array($currentParentId, $visitedIds)) {
                return false;
            }

            $visitedIds[] = $currentParentId;
            $parent = static::find($currentParentId);

            if (! $parent) {
                break;
            }

            $currentParentId = $parent->parent_id;
        }

        return true;
    }

    protected static function handleDepartmentData($department)
    {
        if ($department->parent_id) {
            $parent = static::find($department->parent_id);
            $department->parent_path = $parent?->parent_path.$parent?->id.'/';

            $department->master_department_id = static::findTopLevelParentId($parent);
        } else {
            $department->parent_path = '/';
            $department->master_department_id = null;
        }

        $department->complete_name = static::getCompleteName($department);
    }

    protected static function findTopLevelParentId($department)
    {
        $currentDepartment = $department;

        while ($currentDepartment->parent_id) {
            $currentDepartment = static::find($currentDepartment->parent_id);
        }

        return $currentDepartment->id;
    }

    protected static function getCompleteName($department)
    {
        $names = [];
        $names[] = $department->name;

        $currentDepartment = $department;
        while ($currentDepartment->parent_id) {
            $currentDepartment = static::find($currentDepartment->parent_id);
            array_unshift($names, $currentDepartment->name);
        }

        return implode(' / ', $names);
    }
}
