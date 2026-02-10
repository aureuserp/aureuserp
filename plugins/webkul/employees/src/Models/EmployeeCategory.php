<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Database\Factories\EmployeeCategoryFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;

class EmployeeCategory extends Model
{
    use HasCustomFields, HasFactory;

    protected $table = 'employees_categories';

    protected $fillable = ['name', 'color', 'creator_id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employeeCategory) {
            $employeeCategory->creator_id ??= Auth::id();

            $employeeCategory->color ??= random_color();
        });
    }

    protected static function newFactory(): EmployeeCategoryFactory
    {
        return EmployeeCategoryFactory::new();
    }
}
