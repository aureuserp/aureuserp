<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<\App\Models\MoveLine>
 */
class MoveLineFactory extends Factory
{
    protected $model = MoveLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 0, 1000);
        $isDebit = $this->faker->boolean();

        return [
            'sort'                     => 0,
            'move_id'                  => Move::factory(),
            'journal_id'               => Journal::factory(),
            'company_id'               => Company::factory(),
            'company_currency_id'      => Currency::factory(),
            'reconcile_id'             => null,
            'payment_id'               => null,
            'tax_repartition_line_id'  => null,
            'account_id'               => Account::factory(),
            'currency_id'              => Currency::factory(),
            'partner_id'               => null,
            'group_tax_id'             => null,
            'tax_line_id'              => null,
            'tax_group_id'             => null,
            'statement_id'             => null,
            'statement_line_id'        => null,
            'product_id'               => null,
            'uom_id'                   => null,
            'created_by'               => User::factory(),
            'move_name'                => null,
            'parent_state'             => MoveState::DRAFT,
            'reference'                => null,
            'name'                     => $this->faker->sentence(3),
            'matching_number'          => null,
            'display_type'             => null,
            'date'                     => $this->faker->date(),
            'invoice_date'             => null,
            'date_maturity'            => null,
            'discount_date'            => null,
            'analytic_distribution'    => null,
            'debit'                    => $isDebit ? $amount : 0.0,
            'credit'                   => $isDebit ? 0.0 : $amount,
            'balance'                  => $isDebit ? $amount : -$amount,
            'amount_currency'          => 0.0,
            'tax_base_amount'          => 0.0,
            'amount_residual'          => $isDebit ? $amount : -$amount,
            'amount_residual_currency' => 0.0,
            'quantity'                 => 0.0,
            'price_unit'               => 0.0,
            'price_subtotal'           => 0.0,
            'price_total'              => 0.0,
            'discount'                 => 0.0,
            'discount_amount_currency' => 0.0,
            'discount_balance'         => 0.0,
            'is_imported'              => false,
            'tax_tag_invert'           => false,
            'reconciled'               => false,
            'is_downpayment'           => false,
            'full_reconcile_id'        => null,
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
            'reconciled'      => true,
            'amount_residual' => 0.0,
        ]);
    }

    public function lineSection(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_type' => DisplayType::LINE_SECTION,
            'debit'        => 0.0,
            'credit'       => 0.0,
            'balance'      => 0.0,
        ]);
    }

    public function withProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => Product::factory(),
            'uom_id'     => UOM::factory(),
            'quantity'   => $this->faker->numberBetween(1, 10),
            'price_unit' => $this->faker->randomFloat(2, 10, 100),
        ]);
    }

    public function withQuantityAndPrice(float $quantity, float $priceUnit, bool $isDebit = true): static
    {
        $subtotal = $quantity * $priceUnit;

        return $this->state(fn (array $attributes) => [
            'quantity'         => $quantity,
            'price_unit'       => $priceUnit,
            'price_subtotal'   => $subtotal,
            'price_total'      => $subtotal,
            'debit'            => $isDebit ? $subtotal : 0.0,
            'credit'           => $isDebit ? 0.0 : $subtotal,
            'balance'          => $isDebit ? $subtotal : -$subtotal,
            'amount_residual'  => $isDebit ? $subtotal : -$subtotal,
            'amount_currency'  => $isDebit ? $subtotal : -$subtotal,
        ]);
    }
}
