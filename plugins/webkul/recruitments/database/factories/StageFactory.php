<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\Stage;
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
            'name' => fake()->words(2, true),
            'sort' => 1,
            'is_default' => false,
            'hired_stage' => false,
            'fold' => false,
            'legend_blocked' => null,
            'legend_done' => null,
            'legend_normal' => null,
            'requirements' => null,
            'creator_id' => User::factory(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function hired(): static
    {
        return $this->state(fn (array $attributes) => [
            'hired_stage' => true,
        ]);
    }

    public function folded(): static
    {
        return $this->state(fn (array $attributes) => [
            'fold' => true,
        ]);
    }

    public function withLegend(): static
    {
        return $this->state(fn (array $attributes) => [
            'legend_blocked' => fake()->word(),
            'legend_done' => fake()->word(),
            'legend_normal' => fake()->word(),
        ]);
    }

    public function withRequirements(): static
    {
        return $this->state(fn (array $attributes) => [
            'requirements' => fake()->sentence(),
        ]);
    }
}
