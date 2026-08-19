<?php

use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmployeeResume;

class EmployeeHelper
{
    /**
     * Create a minimal employee.
     *
     * Employee::factory() is deliberately avoided here. Its definition builds a
     * Department, which in turn builds an Employee as its manager, so resolving it
     * recurses until the process dies. Every column on employees_employees is
     * nullable or defaulted, so a direct create is both sufficient and cheap.
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
