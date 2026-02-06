<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Calendar;
use Webkul\Employee\Models\CalendarLeaves;
use Webkul\Security\Models\User;

/**
 * @extends Factory<CalendarLeaves>
 */
class CalendarLeavesFactory extends Factory
{
    protected $model = CalendarLeaves::class;

    public function definition(): array
    {
        $dateFrom = $this->faker->dateTimeBetween('now', '+1 year');
        $dateTo = $this->faker->dateTimeBetween($dateFrom, '+1 year');

        return [
            'name'        => $this->faker->words(3, true),
            'time_type'   => $this->faker->randomElement(['morning', 'afternoon', 'full_day']),
            'date_from'   => $dateFrom,
            'date_to'     => $dateTo,
            'calendar_id' => Calendar::factory(),
            'creator_id'  => User::factory(),
        ];
    }
}
