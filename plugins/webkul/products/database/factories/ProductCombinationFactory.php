<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\ProductCombination;

class ProductCombinationFactory extends Factory
{
    protected $model = ProductCombination::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id'                 => 1,
            'product_attribute_id'       => 1,
            'product_attribute_value_id' => 1,
        ];
    }
}
