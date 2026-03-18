<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
{
    protected $model = State::class;

    public function definition(): array
    {
        return [
            'code'       => fake()->unique()->stateAbbr(),
            'name'       => fake()->state(),
            'country_id' => Country::factory(),
        ];
    }
}
