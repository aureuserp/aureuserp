<?php

namespace Webkul\Payment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentTransaction;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<\App\Models\PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(4, 10, 5000);

        return [
            'sort'                => 0,
            'move_id'             => Move::factory(),
            'journal_id'          => Journal::factory(),
            'company_id'          => Company::factory(),
            'statement_id'        => null,
            'partner_id'          => null,
            'currency_id'         => Currency::factory(),
            'foreign_currency_id' => null,
            'created_id'          => User::factory(),
            'account_number'      => $this->faker->numerify('############'),
            'partner_name'        => null,
            'transaction_type'    => $this->faker->randomElement(['debit', 'credit']),
            'payment_reference'   => $this->faker->numerify('PAY-#####'),
            'internal_index'      => $this->faker->numerify('###'),
            'transaction_details' => [
                'method' => 'card',
                'status' => 'completed',
            ],
            'amount'          => $amount,
            'amount_currency' => $amount,
            'amount_residual' => $amount,
            'is_reconciled'   => false,
        ];
    }

    public function withStatement(): static
    {
        return $this->state(fn (array $attributes) => [
            'statement_id' => BankStatement::factory(),
        ]);
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id'   => Partner::factory(),
            'partner_name' => $this->faker->company(),
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
