<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Models\LeaveMandatoryDay;

/**
 * @extends Factory<\App\Models\LeaveMandatoryDay>
 */
class LeaveMandatoryDayFactory extends Factory
{
    protected $model = LeaveMandatoryDay::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate, '+7 days');

        return [
            'company_id' => Company::factory(),
            'creator_id' => User::factory(),
            'color'      => $this->faker->numberBetween(1, 10),
            'name'       => $this->faker->words(3, true),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
        ];
    }
}
