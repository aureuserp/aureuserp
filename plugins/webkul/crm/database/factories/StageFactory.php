<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;

class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->word(),
            'is_won'     => false,
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function won(): static
    {
        return $this->state(fn (): array => [
            'is_won' => true,
        ]);
    }
}
