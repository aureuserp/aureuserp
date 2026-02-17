<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\CashRounding;
use Webkul\Security\Models\User;

class CashRoundingFactory extends Factory
{
    protected $model = CashRounding::class;

    public function definition(): array
    {
        return [
            'strategy'          => RoundingStrategy::BIGGEST_TAX,
            'rounding_method'   => RoundingMethod::HALF_UP,
            'name'              => $this->faker->words(2, true),
            'rounding'          => 0.05,
            'profit_account_id' => Account::factory(),
            'loss_account_id'   => Account::factory(),
            'creator_id'        => User::factory(),
        ];
    }

    public function addInvoiceLines(): static
    {
        return $this->state(fn (array $attributes) => [
            'strategy' => RoundingStrategy::ADD_INVOICE_LINE,
        ]);
    }
}
