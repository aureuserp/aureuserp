<?php

use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmployeeResume;

class EmployeeHelper
{
    /**
     * Create a minimal employee.
     *
     * Employee::factory() builds a department, a job position and a work location
     * along with it. Every column on employees_employees is nullable or defaulted, so
     * a direct create stays cheaper for tests that only need the row.
     */
    public static function employee(array $attributes = []): Employee
    {
        return Employee::create(array_merge([
            'name' => 'Test Employee',
        ], $attributes));
    }

    /**
     * Create a resume line belonging to an employee, creating the employee when one
     * is not supplied.
     */
    public static function resume(array $attributes = [], ?Employee $employee = null): EmployeeResume
    {
        $employee ??= static::employee();

        return EmployeeResume::factory()->create(array_merge([
            'employee_id' => $employee->id,
        ], $attributes));
    }
}
