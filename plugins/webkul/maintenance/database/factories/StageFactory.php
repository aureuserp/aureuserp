<?php

namespace Webkul\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Maintenance\Models\Stage;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Stage>
 */
class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        return [
            'sort'       => fake()->numberBetween(1, 100),
            'name'       => fake()->randomElement(['New Request', 'In Progress', 'Repaired', 'Scrap']),
            'done'       => false,
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'done' => true,
        ]);
    }
}
