<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Models\Floor;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Floor>
 */
class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'name'             => fake()->unique()->words(2, true),
            'sort'             => fake()->numberBetween(1, 20),
            'background_color' => fake()->hexColor(),
            'creator_id'       => User::query()->value('id') ?? User::factory(),
        ];
    }
}
