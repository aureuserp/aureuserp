<?php

namespace Webkul\Attendance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'employee_id' => Employee::factory(),
            'work_date'   => $checkIn->format('Y-m-d'),
            'check_in'    => $checkIn->format('Y-m-d H:i:s'),
            'check_out'   => null,
            'source'      => 'manual',
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_out' => date('Y-m-d H:i:s', strtotime($attributes['check_in'].' +8 hours')),
        ]);
    }
}
