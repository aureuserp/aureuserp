<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\RefuseReason;
use Webkul\Security\Models\User;

/**
 * @extends Factory<RefuseReason>
 */
class RefuseReasonFactory extends Factory
{
    protected $model = RefuseReason::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sort' => 1,
            'template' => null,
            'is_active' => true,
            'creator_id' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withTemplate(): static
    {
        return $this->state(fn (array $attributes) => [
            'template' => fake()->paragraph(),
        ]);
    }
}
