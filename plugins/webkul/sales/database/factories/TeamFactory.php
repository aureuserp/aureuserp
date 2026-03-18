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
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'            => fake()->randomNumber(),
            'company_id'      => null,
            'user_id'         => null,
            'color'           => fake()->hexColor,
            'creator_id'      => null,
            'name'            => fake()->name,
            'is_active'       => fake()->boolean,
            'invoiced_target' => fake()->randomNumber(),
        ];
    }
}
