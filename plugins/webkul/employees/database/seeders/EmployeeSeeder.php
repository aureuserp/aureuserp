<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_employees')->delete();

        $user = User::first();

        $employees = [
            [
                'time_zone'      => 'UTC',
                'creator_id'     => $user?->id,
                'name'           => 'Paul Williams',
                'job_title'      => 'Experienced Developer',
                'work_email'     => 'paul@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'America/New_York',
                'creator_id'     => $user?->id,
                'name'           => 'John Doe',
                'job_title'      => 'Junior Developer',
                'work_email'     => 'john@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'Europe/London',
                'creator_id'     => $user?->id,
                'name'           => 'Jane Smith',
                'job_title'      => 'Project Manager',
                'work_email'     => 'jane@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'Asia/Kolkata',
                'creator_id'     => $user?->id,
                'name'           => 'Ravi Kumar',
                'job_title'      => 'Team Lead',
                'work_email'     => 'ravi@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'Australia/Sydney',
                'creator_id'     => $user?->id,
                'name'           => 'Emily Davis',
                'job_title'      => 'QA Engineer',
                'work_email'     => 'emily@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'America/Los_Angeles',
                'creator_id'     => $user?->id,
                'name'           => 'Michael Brown',
                'job_title'      => 'UX Designer',
                'work_email'     => 'michael@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'Asia/Tokyo',
                'creator_id'     => $user?->id,
                'name'           => 'Hiro Tanaka',
                'job_title'      => 'Backend Developer',
                'work_email'     => 'hiro@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'Africa/Johannesburg',
                'creator_id'     => $user?->id,
                'name'           => 'Linda Ndlovu',
                'job_title'      => 'HR Manager',
                'work_email'     => 'linda@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'Europe/Berlin',
                'creator_id'     => $user?->id,
                'name'           => 'Hans Müller',
                'job_title'      => 'Frontend Developer',
                'work_email'     => 'hans@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
            [
                'time_zone'      => 'America/Chicago',
                'creator_id'     => $user?->id,
                'name'           => 'Grace Wilson',
                'job_title'      => 'Data Scientist',
                'work_email'     => 'grace@example.com',
                'employee_type'  => 'employee',
                'is_active'      => true,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create(array_merge($employee, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
