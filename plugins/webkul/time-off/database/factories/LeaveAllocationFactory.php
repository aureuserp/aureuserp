<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\LeaveAccrualPlan;
use Webkul\TimeOff\Models\LeaveAllocation;
use Webkul\TimeOff\Models\LeaveType;

/**
 * @extends Factory<\App\Models\LeaveAllocation>
 */
class LeaveAllocationFactory extends Factory
{
    protected $model = LeaveAllocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateFrom = $this->faker->dateTimeBetween('-1 year', 'now');
        $dateTo = $this->faker->dateTimeBetween('now', '+1 year');
        $numberOfDays = 20;

        return [
            'holiday_status_id'                 => LeaveType::factory(),
            'employee_id'                       => Employee::factory(),
            'employee_company_id'               => null,
            'manager_id'                        => null,
            'approver_id'                       => null,
            'second_approver_id'                => null,
            'department_id'                     => null,
            'accrual_plan_id'                   => null,
            'creator_id'                        => User::factory(),
            'name'                              => $this->faker->words(3, true),
            'state'                             => State::CONFIRM,
            'allocation_type'                   => AllocationType::REGULAR,
            'date_from'                         => $dateFrom->format('Y-m-d'),
            'date_to'                           => $dateTo->format('Y-m-d'),
            'last_executed_carryover_date'      => null,
            'last_called'                       => null,
            'actual_last_called'                => null,
            'next_call'                         => null,
            'carried_over_days_expiration_date' => null,
            'notes'                             => null,
            'already_accrued'                   => 0.0,
            'number_of_days'                    => $numberOfDays,
            'number_of_hours_display'           => ($numberOfDays * 8).' hours',
            'yearly_accrued_amount'             => 0.0,
            'expiring_carryover_days'           => 0.0,
        ];
    }

    public function accrual(): static
    {
        return $this->state(fn (array $attributes) => [
            'allocation_type' => AllocationType::ACCRUAL,
            'accrual_plan_id' => LeaveAccrualPlan::factory(),
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'       => State::VALIDATE_ONE,
            'approver_id' => User::factory(),
        ]);
    }

    public function withDepartment(): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => Department::factory(),
        ]);
    }
}
