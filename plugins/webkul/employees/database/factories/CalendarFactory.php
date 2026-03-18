<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Calendar;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class CalendarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Calendar::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                      => fake()->name,
            'tz'                        => fake()->timezone,
            'hours_per_day'             => fake()->randomFloat(2, 0, 24),
            'status'                    => 1,
            'two_weeks_calendar'        => 0,
            'flexible_hours'            => 0,
            'full_time_required_hours'  => 0,
            'user_id'                   => User::query()->value('id') ?? User::factory(),
            'company_id'                => Company::factory(),
        ];
    }
}
