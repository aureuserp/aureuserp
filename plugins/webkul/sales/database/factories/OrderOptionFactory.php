<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\Product;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Models\OrderOption;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<OrderOption>
 */
class OrderOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 10);
        $priceUnit = $this->faker->randomFloat(4, 10, 500);
        $discount = 0;

        return [
            'sort'       => $this->faker->numberBetween(1, 100),
            'name'       => $this->faker->sentence(3),
            'quantity'   => $quantity,
            'price_unit' => $priceUnit,
            'discount'   => $discount,
            'order_id'   => Order::factory(),
            'product_id' => Product::factory(),
            'line_id'    => null,
            'uom_id'     => UOM::factory(),
            'creator_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the option has a discount.
     */
    public function withDiscount(?float $discount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'discount' => $discount ?? $this->faker->randomFloat(2, 5, 20),
        ]);
    }

    /**
     * Indicate that the option is linked to an order line.
     */
    public function withOrderLine(): static
    {
        return $this->state(fn (array $attributes) => [
            'line_id' => OrderLine::factory(),
        ]);
    }
}
