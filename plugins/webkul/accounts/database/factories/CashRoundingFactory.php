<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\CashRounding;
use Webkul\Security\Models\User;

class CashRoundingFactory extends Factory
{
    protected $model = CashRounding::class;

    public function definition(): array
    {
        return [
            'creator_id'        => User::factory(),
            'strategy'          => 'biggest_tax',
            'rounding_method'   => 'half_up',
            'name'              => $this->faker->words(2, true),
            'rounding'          => 0.05,
            'profit_account_id' => Account::factory(),
            'loss_account_id'   => Account::factory(),
        ];
    }

    public function addInvoiceLines(): static
    {
        return $this->state(fn (array $attributes) => [
            'strategy' => 'add_invoice_line',
        ]);
    }
}
