<?php

namespace Webkul\Account\Database\Factories;

use Webkul\Account\Models\Account;
use Webkul\Account\Models\Category;
use Webkul\Product\Database\Factories\CategoryFactory as BaseCategoryFactory;

/**
 * @extends BaseCategoryFactory
 */
class CategoryFactory extends BaseCategoryFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'property_account_income_id'       => null,
            'property_account_expense_id'      => null,
            'property_account_down_payment_id' => null,
        ]);
    }

    /**
     * Indicate that the category has income account.
     */
    public function withIncomeAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_income_id' => Account::factory(),
        ]);
    }

    /**
     * Indicate that the category has expense account.
     */
    public function withExpenseAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_expense_id' => Account::factory(),
        ]);
    }

    /**
     * Indicate that the category has down payment account.
     */
    public function withDownPaymentAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_down_payment_id' => Account::factory(),
        ]);
    }

    /**
     * Indicate that the category has all accounts.
     */
    public function withAllAccounts(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_account_income_id'       => Account::factory(),
            'property_account_expense_id'      => Account::factory(),
            'property_account_down_payment_id' => Account::factory(),
        ]);
    }
}
