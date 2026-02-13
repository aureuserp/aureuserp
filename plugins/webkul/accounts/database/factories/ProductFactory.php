<?php

namespace Webkul\Account\Database\Factories;

use Webkul\Account\Models\Account;
use Webkul\Account\Models\Product;
use Webkul\Product\Database\Factories\ProductFactory as BaseProductFactory;

/**
 * @extends BaseProductFactory
 */
class ProductFactory extends BaseProductFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'property_account_income_id'  => null,
            'property_account_expense_id' => null,
            'image'                       => null,
            'service_type'                => null,
            'sale_line_warn'              => null,
            'expense_policy'              => null,
            'invoice_policy'              => null,
            'sale_line_warn_msg'          => null,
            'sales_ok'                    => true,
            'purchase_ok'                 => true,
        ]);
    }

    /**
     * Indicate that the product has income account.
     */
    public function withIncomeAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_income_id' => Account::factory(),
        ]);
    }

    /**
     * Indicate that the product has expense account.
     */
    public function withExpenseAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_expense_id' => Account::factory(),
        ]);
    }

    /**
     * Indicate that the product has both income and expense accounts.
     */
    public function withAccounts(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_income_id'  => Account::factory(),
            'property_account_expense_id' => Account::factory(),
        ]);
    }
}
