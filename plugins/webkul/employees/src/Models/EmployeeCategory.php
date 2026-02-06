<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Database\Factories\EmployeeCategoryFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Illuminate\Support\Facades\Auth;

class EmployeeCategory extends Model
{
    use HasCustomFields, HasFactory;

    protected $table = 'employees_categories';

    protected $fillable = ['name', 'color', 'creator_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employeeCategory) {
            $employeeCategory->creator_id = Auth::id();

            $employeeCategory->color ??= random_color();

        });
    }

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): EmployeeCategoryFactory
    {
        return EmployeeCategoryFactory::new();
    }
}
