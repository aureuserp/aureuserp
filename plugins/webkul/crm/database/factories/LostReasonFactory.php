<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Models\LostReason;
use Webkul\Security\Models\User;

class LostReasonFactory extends Factory
{
    protected $model = LostReason::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->sentence(3),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'deleted_at' => now(),
        ]);
    }
}
