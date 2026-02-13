<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'currency_id'  => Currency::factory(),
            'creator_id'   => User::factory(),
            'account_type' => AccountType::ASSET_CURRENT,
            'name'         => $this->faker->words(3, true),
            'code'         => $this->faker->unique()->numerify('###'),
            'note'         => null,
            'deprecated'   => false,
            'reconcile'    => false,
            'non_trade'    => false,
        ];
    }

    public function receivable(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::ASSET_RECEIVABLE,
            'reconcile'    => true,
        ]);
    }

    public function payable(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::LIABILITY_PAYABLE,
            'reconcile'    => true,
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::EXPENSE,
        ]);
    }
}
