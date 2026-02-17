<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Journal;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<\App\Models\BankStatementLine>
 */
class BankStatementLineFactory extends Factory
{
    protected $model = BankStatementLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'                => 0,
            'journal_id'          => Journal::factory(),
            'company_id'          => Company::factory(),
            'statement_id'        => BankStatement::factory(),
            'partner_id'          => null,
            'currency_id'         => Currency::factory(),
            'foreign_currency_id' => null,
            'created_by'          => User::factory(),
            'account_number'      => $this->faker->optional()->numerify('############'),
            'partner_name'        => $this->faker->optional()->company(),
            'transaction_type'    => null,
            'payment_reference'   => $this->faker->optional()->bothify('PAY-####'),
            'internal_index'      => null,
            'transaction_details' => null,
            'amount'              => $this->faker->randomFloat(2, -1000, 1000),
            'amount_currency'     => null,
            'is_reconciled'       => false,
            'amount_residual'     => null,
        ];
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => Partner::factory(),
        ]);
    }

    public function reconciled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_reconciled'   => true,
            'amount_residual' => 0,
        ]);
    }
}
