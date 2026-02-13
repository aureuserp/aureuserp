<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\FullReconcile;
use Webkul\Account\Models\Move;
use Webkul\Security\Models\User;

/**
 * @extends Factory<\App\Models\FullReconcile>
 */
class FullReconcileFactory extends Factory
{
    protected $model = FullReconcile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exchange_move_id' => null,
            'created_id'       => User::factory(),
        ];
    }

    public function withExchangeMove(): static
    {
        return $this->state(fn (array $attributes) => [
            'exchange_move_id' => Move::factory(),
        ]);
    }
}
