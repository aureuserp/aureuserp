<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Sale\Models\Team;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'            => $this->faker->randomNumber(),
            'company_id'      => null,
            'user_id'         => null,
            'color'           => $this->faker->hexColor,
            'creator_id'      => null,
            'name'            => $this->faker->name,
            'is_active'       => $this->faker->boolean,
            'invoiced_target' => $this->faker->randomNumber(),
        ];
    }
}
