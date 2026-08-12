<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\CalendarAttendance;

class CalendarAttendanceFactory extends Factory
{
    protected $model = CalendarAttendance::class;

    public function definition(): array
    {
        return [
            'sort'              => fake()->randomNumber(),
            'name'              => fake()->word,
            'day_of_week'       => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'day_period'        => fake()->randomElement(['morning', 'afternoon', 'evening']),
            'week_type'         => fake()->randomElement(['odd', 'even', 'both']),
            'display_type'      => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'date_from'         => fake()->date(),
            'date_to'           => fake()->date(),
            'hour_from'         => fake()->time(),
            'hour_to'           => fake()->time(),
            'duration_days'     => fake()->randomNumber(),
            'calendar_id'       => fake()->randomNumber(),
            'creator_id'        => User::query()->value('id') ?? User::factory(),
        ];
    }
}
