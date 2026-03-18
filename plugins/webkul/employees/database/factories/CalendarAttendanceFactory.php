<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\CalendarAttendance;
use Webkul\Security\Models\User;

class CalendarAttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CalendarAttendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sequence'          => fake()->randomNumber(),
            'name'              => fake()->word,
            'day_of_week'       => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'day_period'        => fake()->randomElement(['morning', 'afternoon', 'evening']),
            'week_type'         => fake()->randomElement(['odd', 'even', 'both']),
            'display_type'      => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'date_from'         => fake()->date(),
            'date_to'           => fake()->date(),
            'hour_from'         => fake()->time(),
            'hour_to'           => fake()->time(),
            'durations_days'    => fake()->randomNumber(),
            'calendar_id'       => fake()->randomNumber(),
            'user_id'           => User::query()->value('id') ?? User::factory(),
        ];
    }
}
