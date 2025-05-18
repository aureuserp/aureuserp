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
                \Log::error('Circular reference detected during creation', [
                    'department_id' => $department->id,
                    'parent_id'     => $department->parent_id,
                ]);
                throw new InvalidArgumentException('Circular reference detected in department hierarchy');
            }
            static::handleDepartmentData($department);
        });

        static::updating(function ($department) {
            if (! static::validateNoRecursion($department)) {
                \Log::error('Circular reference detected during update', [
                    'department_id' => $department->id,
                    'parent_id'     => $department->parent_id,
                ]);
                throw new InvalidArgumentException('Circular reference detected in department hierarchy');
            }
            static::handleDepartmentData($department);
        });
    }

    /**
     * Validates that there is no circular reference in the department hierarchy.
     * Robust against unsaved/new models and missing parents, logs for debug.
     */
    protected static function validateNoRecursion($department)
    {
        // No parent, no cycle possible
        if (! $department->parent_id) {
            return true;
        }

        // If updating, make sure not setting itself as parent
        if ($department->id && $department->parent_id == $department->id) {
            \Log::error('Department parent_id set to self', [
                'department_id' => $department->id,
                'parent_id'     => $department->parent_id,
            ]);

            return false;
        }

        $currentParentId = $department->parent_id;
        $visitedIds = $department->id ? [$department->id] : [];
        $logPath = [];

        while ($currentParentId) {
            if (in_array($currentParentId, $visitedIds)) {
                // Log for debug
                \Log::error('Circular reference detected in parent chain', [
                    'department_id' => $department->id,
                    'parent_id'     => $department->parent_id,
                    'visited'       => $visitedIds,
                    'log_path'      => $logPath,
                ]);

                return false;
            }
            $visitedIds[] = $currentParentId;
            $logPath[] = $currentParentId;
            $parent = static::query()->select(['id', 'parent_id'])->find($currentParentId);
            if (! $parent) {
                // Defensive: break if parent not found
                break;
            }
            $currentParentId = $parent->parent_id;
        }

        return true;
    }

    /**
     * Handles assignment of parent_path, master_department_id, and complete_name.
     * Uses batch queries for performance.
     */
    protected static function handleDepartmentData($department)
    {
        if ($department->parent_id) {
            $parent = static::query()
                ->select(['id', 'parent_path', 'master_department_id', 'parent_id', 'name'])
                ->find($department->parent_id);

            if ($parent) {
                $department->parent_path = ($parent->parent_path ?? '/').$parent->id.'/';
                $department->master_department_id = static::findTopLevelParentId($parent);
            } else {
                // Defensive: parent doesn't exist
                $department->parent_path = '/';
                $department->master_department_id = null;
            }
        } else {
            $department->parent_path = '/';
            $department->master_department_id = null;
        }

        $department->complete_name = static::getCompleteName($department);
    }

    /**
     * Finds the top-level parent id efficiently.
     */
    protected static function findTopLevelParentId($department)
    {
        if (! $department) {
            return null;
        }

        $visited = [];
        $current = $department;
        while ($current && $current->parent_id) {
            if (in_array($current->id, $visited)) {
                // Defensive: just in case, break on cycle
                break;
            }
            $visited[] = $current->id;
            $current = static::query()
                ->select(['id', 'parent_id'])
                ->find($current->parent_id);
        }

        return $current ? $current->id : null;
    }

    /**
     * Builds the complete name efficiently (single query for all ancestors).
     */
    protected static function getCompleteName($department)
    {
        if (! $department) {
            return '';
        }

        $names = [];
        $visited = [];
        $current = $department;

        while ($current) {
            // Use spl_object_id as fallback for unsaved models
            $uniqueId = $current->id ?? spl_object_id($current);
            if (in_array($uniqueId, $visited)) {
                // Defensive: break on cycle
                break;
            }
            $visited[] = $uniqueId;
            array_unshift($names, $current->name);

            if (! $current->parent_id) {
                break;
            }
            $current = static::query()
                ->select(['id', 'parent_id', 'name'])
                ->find($current->parent_id);
        }

        return implode(' / ', $names);
    }
}
