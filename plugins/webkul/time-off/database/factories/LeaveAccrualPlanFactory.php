<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Enums\AccruedGainTime;
use Webkul\TimeOff\Enums\CarryoverDate;
use Webkul\TimeOff\Enums\CarryoverDay;
use Webkul\TimeOff\Enums\CarryoverMonth;
use Webkul\TimeOff\Models\LeaveAccrualPlan;
use Webkul\TimeOff\Models\LeaveType;

/**
 * @extends Factory<\App\Models\LeaveAccrualPlan>
 */
class LeaveAccrualPlanFactory extends Factory
{
    protected $model = LeaveAccrualPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'time_off_type_id'        => LeaveType::factory(),
            'company_id'              => Company::factory(),
            'carryover_day'           => CarryoverDay::DAY_1,
            'creator_id'              => User::factory(),
            'name'                    => $this->faker->words(3, true),
            'transition_mode'         => 'immediately',
            'accrued_gain_time'       => AccruedGainTime::START,
            'carryover_date'          => CarryoverDate::YEAR_START,
            'carryover_month'         => CarryoverMonth::JAN,
            'added_value_type'        => 'days',
            'is_active'               => true,
            'is_based_on_worked_time' => false,
        ];
    }

    public function endOfPeriod(): static
    {
        return $this->state(fn (array $attributes) => [
            'accrued_gain_time' => AccruedGainTime::END,
        ]);
    }
}
