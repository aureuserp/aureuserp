<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Stage>
 */
class StageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->word(),
            'is_won'     => false,
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    /**
     * Indicate that the stage marks opportunities as won.
     */
    public function won(): static
    {
        return $this->state(fn (): array => [
            'is_won' => true,
        ]);
    }
}
