<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Calendar;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveType;

/**
 * @extends Factory<\App\Models\Leave>
 */
class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestDateFrom = $this->faker->dateTimeBetween('now', '+30 days');
        $requestDateTo = $this->faker->dateTimeBetween($requestDateFrom, '+7 days');
        $numberOfDays = $requestDateFrom->diff($requestDateTo)->days + 1;

        return [
            'user_id'                  => User::factory(),
            'manager_id'               => null,
            'holiday_status_id'        => LeaveType::factory(),
            'employee_id'              => Employee::factory(),
            'employee_company_id'      => null,
            'company_id'               => Company::factory(),
            'department_id'            => null,
            'calendar_id'              => null,
            'meeting_id'               => null,
            'first_approver_id'        => null,
            'second_approver_id'       => null,
            'creator_id'               => User::factory(),
            'private_name'             => $this->faker->sentence(3),
            'attachment'               => null,
            'state'                    => State::CONFIRM,
            'duration_display'         => $numberOfDays.' '.($numberOfDays > 1 ? 'days' : 'day'),
            'request_date_from_period' => RequestDateFromPeriod::MORNING,
            'request_date_from'        => $requestDateFrom->format('Y-m-d'),
            'request_date_to'          => $requestDateTo->format('Y-m-d'),
            'notes'                    => null,
            'request_unit_half'        => false,
            'request_unit_hours'       => false,
            'date_from'                => $requestDateFrom->format('Y-m-d'),
            'date_to'                  => $requestDateTo->format('Y-m-d'),
            'number_of_days'           => $numberOfDays,
            'number_of_hours'          => $numberOfDays * 8,
            'request_hour_from'        => '08:00:00',
            'request_hour_to'          => '17:00:00',
        ];
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'             => State::VALIDATE_ONE,
            'first_approver_id' => User::factory(),
        ]);
    }

    public function withDepartment(): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => Department::factory(),
        ]);
    }

    public function withCalendar(): static
    {
        return $this->state(fn (array $attributes) => [
            'calendar_id' => Calendar::factory(),
        ]);
    }
}
