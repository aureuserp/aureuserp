<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Sale\Models\AdvancedPaymentInvoice;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<AdvancedPaymentInvoice>
 */
class AdvancedPaymentInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdvancedPaymentInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 100, 5000);

        return [
            'advance_payment_method' => null,
            'fixed_amount'           => null,
            'deduct_down_payments'   => false,
            'consolidated_billing'   => false,
            'amount'                 => $amount,
            'currency_id'            => Currency::factory(),
            'company_id'             => Company::factory(),
            'creator_id'             => User::factory(),
        ];
    }

    /**
     * Indicate that down payments should be deducted.
     */
    public function deductDownPayments(): static
    {
        return $this->state(fn (array $attributes) => [
            'deduct_down_payments' => true,
        ]);
    }

    /**
     * Indicate that billing should be consolidated.
     */
    public function consolidatedBilling(): static
    {
        return $this->state(fn (array $attributes) => [
            'consolidated_billing' => true,
        ]);
    }
}
