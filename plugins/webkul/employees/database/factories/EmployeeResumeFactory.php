<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmployeeResume;
use Webkul\Employee\Models\EmployeeResumeLineType;
use Webkul\Security\Models\User;

/**
 * @extends Factory<EmployeeResume>
 */
class EmployeeResumeFactory extends Factory
{
    protected $model = EmployeeResume::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-5 years', '-1 year');
        $endDate = fake()->optional()->dateTimeBetween($startDate, 'now');

        return [
            'name'                         => fake()->sentence(4),
            'display_type'                 => null,
            'start_date'                   => $startDate,
            'end_date'                     => $endDate,
            'description'                  => fake()->optional()->paragraph(),
            'employee_id'                  => Employee::factory(),
            'employee_resume_line_type_id' => EmployeeResumeLineType::factory(),
            'creator_id'                   => User::query()->value('id') ?? User::factory(),
            'user_id'                      => null,
        ];
    }
}
