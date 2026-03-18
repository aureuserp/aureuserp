<?php

namespace Webkul\Purchase\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Models\RequisitionLine;
use Webkul\Security\Models\User;

/**
 * @extends Factory<RequisitionLine>
 */
class RequisitionLineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequisitionLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'qty'        => fake()->numberBetween(1, 100),
            'price_unit' => fake()->randomFloat(2, 10, 1000),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
