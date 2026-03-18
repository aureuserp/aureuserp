<?php

namespace Webkul\Account\Database\Factories;

use Webkul\Account\Models\Account;
use Webkul\Account\Models\Partner;
use Webkul\Partner\Database\Factories\PartnerFactory as BasePartnerFactory;

/**
 * @extends BasePartnerFactory
 */
class PartnerFactory extends BasePartnerFactory
{
    protected $model = Partner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'property_account_payable_id'    => null,
            'property_account_receivable_id' => null,
        ]);
    }

    public function withPayableAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_payable_id' => Account::factory()->payable(),
        ]);
    }

    public function withReceivableAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_receivable_id' => Account::factory()->receivable(),
        ]);
    }

    public function withAccounts(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_payable_id'    => Account::factory()->payable(),
            'property_account_receivable_id' => Account::factory()->receivable(),
        ]);
    }
}
