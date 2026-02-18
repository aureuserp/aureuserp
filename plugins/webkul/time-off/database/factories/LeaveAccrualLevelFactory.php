<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\LeaveAccrualLevel;
use Webkul\TimeOff\Models\LeaveAccrualPlan;

/**
 * @extends Factory<\App\Models\LeaveAccrualLevel>
 */
class LeaveAccrualLevelFactory extends Factory
{
    protected $model = LeaveAccrualLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'                        => 0,
            'accrual_plan_id'             => LeaveAccrualPlan::factory(),
            'start_count'                 => 0,
            'first_day'                   => 1,
            'second_day'                  => 1,
            'first_month_day'             => 1,
            'second_month_day'            => 1,
            'yearly_day'                  => 1,
            'postpone_max_days'           => 0,
            'accrual_validity_count'      => 0,
            'creator_id'                  => User::factory(),
            'start_type'                  => 'day',
            'added_value_type'            => 'days',
            'frequency'                   => 'daily',
            'week_day'                    => 'monday',
            'first_month'                 => 'january',
            'second_month'                => 'january',
            'yearly_month'                => 'january',
            'action_with_unused_accruals' => 'lost',
            'accrual_validity_type'       => 'year',
            'added_value'                 => 1.0,
            'maximum_leave'               => 0,
            'maximum_leave_yearly'        => 0,
            'cap_accrued_time'            => false,
            'cap_accrued_time_yearly'     => false,
            'accrual_validity'            => false,
        ];
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'monthly',
        ]);
    }

    public function yearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'yearly',
        ]);
    }
}
