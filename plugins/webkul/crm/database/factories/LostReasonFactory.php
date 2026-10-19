<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Models\LostReason;
use Webkul\Security\Models\User;

/**
 * @extends Factory<LostReason>
 */
class LostReasonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LostReason::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->sentence(3),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    /**
     * Indicate that the lost reason is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (): array => [
            'deleted_at' => now(),
        ]);
    }
}
