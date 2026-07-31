<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Calendar;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class CalendarFactory extends Factory
{
    use HasCompanyDefault;

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
            'two_weeks_calendar'        => false,
            'flexible_hours'            => false,
            'full_time_required_hours'  => 0,
            'user_id'                   => User::query()->value('id') ?? User::factory(),
        ];
    }
}
