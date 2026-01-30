<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentMethod;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'move_id'                             => null,
            'journal_id'                          => Journal::factory(),
            'company_id'                          => Company::factory(),
            'partner_bank_id'                     => null,
            'paired_internal_transfer_payment_id' => null,
            'payment_method_line_id'              => PaymentMethodLine::factory(),
            'payment_method_id'                   => PaymentMethod::factory(),
            'currency_id'                         => Currency::factory(),
            'partner_id'                          => Partner::factory(),
            'outstanding_account_id'              => Account::factory(),
            'destination_account_id'              => Account::factory(),
            'created_by'                          => User::factory(),
            'name'                                => $this->faker->optional()->bothify('PAY/####/####'),
            'state'                               => PaymentStatus::DRAFT,
            'payment_type'                        => PaymentType::SEND,
            'partner_type'                        => 'customer',
            'memo'                                => null,
            'payment_reference'                   => null,
            'date'                                => $this->faker->date(),
            'amount'                              => $this->faker->randomFloat(2, 10, 1000),
            'amount_company_currency_signed'      => 0.0,
            'is_reconciled'                       => false,
            'is_matched'                          => false,
            'is_sent'                             => false,
            'payment_transaction_id'              => null,
            'source_payment_id'                   => null,
            'payment_token_id'                    => null,
        ];
    }

    public function posted(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'   => PaymentStatus::PAID,
            'move_id' => Move::factory(),
        ]);
    }

    public function inbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => PaymentType::RECEIVE,
        ]);
    }

    public function reconciled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_reconciled' => true,
        ]);
    }
}
