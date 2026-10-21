<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Enums\CashMovementType;
use Webkul\PointOfSale\Models\CashMovement;
use Webkul\PointOfSale\Models\Session;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<CashMovement>
 */
class CashMovementFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = CashMovement::class;

    public function definition(): array
    {
        return [
            'type'       => CashMovementType::IN,
            'amount'     => fake()->randomFloat(2, 5, 250),
            'reason'     => fake()->sentence(3),
            'session_id' => Session::query()->value('id') ?? Session::factory(),
            'user_id'    => User::query()->value('id') ?? User::factory(),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function out(): static
    {
        return $this->state(fn () => ['type' => CashMovementType::OUT]);
    }

    public function amount(float $amount): static
    {
        return $this->state(fn () => ['amount' => $amount]);
    }
}
