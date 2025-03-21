<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\TimeOff\Models\LeaveType;

class LeaveTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $leaveNames = [
            'Annual Leave',
            'Sick Leave',
            'Maternity Leave',
            'Paternity Leave',
            'Study Leave',
            'Bereavement Leave',
            'Personal Leave',
            'Unpaid Leave',
            'Work From Home',
            'Compensatory Off',
        ];

        return [
            'sort'                                => fake()->numberBetween(1000, 500000),
            'color'                               => fake()->boolean(30) ? fake()->hexColor() : null,
            'company_id'                          => fake()->numberBetween(1000, 5000),
            'max_allowed_negative'                => fake()->randomElement([1, 2, 3, 5, 10]),
            'creator_id'                          => fake()->boolean(70) ? fake()->numberBetween(1, 5000) : null,
            'leave_validation_type'               => fake()->randomElement(['both', 'manager', 'hr']),
            'requires_allocation'                 => fake()->boolean(),
            'employee_requests'                   => fake()->boolean(),
            'allocation_validation_type'          => 'hr',
            'time_type'                           => 'leave',
            'request_unit'                        => fake()->randomElement(['day', 'hour', 'half_day']),
            'name'                                => fake()->randomElement($leaveNames),
            'create_calendar_meeting'             => fake()->boolean(80),
            'is_active'                           => fake()->boolean(90),
            'show_on_dashboard'                   => fake()->boolean(70),
            'unpaid'                              => fake()->boolean(20),
            'include_public_holidays_in_duration' => fake()->boolean(30),
            'support_document'                    => fake()->boolean(40),
            'allows_negative'                     => fake()->boolean(30),
            'created_at'                          => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at'                          => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
