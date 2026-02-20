<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductSupplier;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<ProductSupplier>
 */
class ProductSupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductSupplier::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'         => fake()->randomNumber(),
            'delay'        => fake()->numberBetween(1, 30),
            'product_name' => fake()->words(3, true),
            'product_code' => fake()->bothify('SUP-####'),
            'starts_at'    => fake()->dateTimeBetween('-1 month', 'now'),
            'ends_at'      => fake()->dateTimeBetween('now', '+1 year'),
            'min_qty'      => fake()->numberBetween(1, 100),
            'price'        => fake()->randomFloat(2, 10, 1000),
            'discount'     => fake()->randomFloat(2, 0, 50),
            'product_id'   => Product::factory(),
            'partner_id'   => Partner::factory(),
            'currency_id'  => Currency::factory(),
            'creator_id'   => User::factory(),
            'company_id'   => Company::factory(),
        ];
    }
}
