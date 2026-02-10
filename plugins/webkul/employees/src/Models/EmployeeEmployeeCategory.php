<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Database\Factories\EmployeeEmployeeCategoryFactory;

class EmployeeEmployeeCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'employees_employee_categories';

    protected $fillable = ['employee_id', 'category_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function category()
    {
        return $this->belongsTo(EmployeeCategory::class, 'category_id');
    }

    protected static function newFactory(): EmployeeEmployeeCategoryFactory
    {
        return EmployeeEmployeeCategoryFactory::new();
    }
}
