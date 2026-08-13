<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\CalendarLeave;

class CalendarLeaveFactory extends Factory
{
    protected $model = CalendarLeave::class;

    public function definition(): array
    {
        $dateFrom = fake()->dateTimeBetween('now', '+1 year');
        $dateTo = fake()->dateTimeBetween($dateFrom, '+1 year');

        return [
            'name'        => fake()->words(3, true),
            'time_type'   => fake()->randomElement(['morning', 'afternoon', 'full_day']),
            'date_from'   => $dateFrom,
            'date_to'     => $dateTo,
            'calendar_id' => Calendar::factory(),
            'creator_id'  => User::query()->value('id') ?? User::factory(),
        ];
    }
}
