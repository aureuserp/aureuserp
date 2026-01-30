<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\Product;
use Webkul\Sale\Models\OrderTemplate;
use Webkul\Sale\Models\OrderTemplateProduct;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<OrderTemplateProduct>
 */
class OrderTemplateProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderTemplateProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->sentence(3),
            'quantity'          => $this->faker->randomFloat(2, 1, 10),
            'display_type'      => null,
            'order_template_id' => OrderTemplate::factory(),
            'company_id'        => Company::factory(),
            'product_id'        => Product::factory(),
            'product_uom_id'    => UOM::factory(),
            'creator_id'        => User::factory(),
        ];
    }
}
