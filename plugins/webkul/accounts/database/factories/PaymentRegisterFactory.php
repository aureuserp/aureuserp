<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\InstallmentMode;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<\App\Models\PaymentRegister>
 */
class PaymentRegisterFactory extends Factory
{
    protected $model = PaymentRegister::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);

        return [
            'currency_id'                 => Currency::factory(),
            'journal_id'                  => Journal::factory(),
            'partner_bank_id'             => null,
            'custom_user_currency_id'     => null,
            'source_currency_id'          => Currency::factory(),
            'company_id'                  => Company::factory(),
            'partner_id'                  => Partner::factory(),
            'payment_method_line_id'      => PaymentMethodLine::factory(),
            'writeoff_account_id'         => null,
            'creator_id'                  => User::factory(),
            'communication'               => null,
            'installments_mode'           => InstallmentMode::FULL,
            'payment_type'                => PaymentType::RECEIVE,
            'partner_type'                => 'customer',
            'payment_difference_handling' => 'open',
            'writeoff_label'              => null,
            'payment_date'                => $this->faker->date(),
            'amount'                      => $amount,
            'custom_user_amount'          => $amount,
            'source_amount'               => $amount,
            'source_amount_currency'      => $amount,
            'group_payment'               => false,
            'can_group_payments'          => false,
            'payment_token_id'            => null,
        ];
    }

    public function writeoff(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_difference_handling' => 'reconcile',
            'writeoff_account_id'         => Account::factory(),
            'writeoff_label'              => 'Write-Off',
        ]);
    }
}
