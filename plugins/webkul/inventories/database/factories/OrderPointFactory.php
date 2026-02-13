<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\OrderPointTrigger;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<OrderPoint>
 */
class OrderPointFactory extends Factory
{
    protected $model = OrderPoint::class;

    public function definition(): array
    {
        return [
            'name'                => fake()->words(2, true),
            'trigger'             => OrderPointTrigger::AUTOMATIC,
            'snoozed_until'       => null,
            'product_min_qty'     => 5.0,
            'product_max_qty'     => 50.0,
            'qty_multiple'        => 1.0,
            'qty_to_order_manual' => null,

            // Relationships
            'product_id'          => Product::factory(),
            'product_category_id' => null,
            'warehouse_id'        => Warehouse::factory(),
            'location_id'         => Location::factory(),
            'route_id'            => null,
            'company_id'          => Company::factory(),
            'creator_id'          => User::factory(),
        ];
    }

    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger'             => OrderPointTrigger::MANUAL,
            'qty_to_order_manual' => 10.0,
        ]);
    }

    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_category_id' => Category::factory(),
        ]);
    }

    public function withRoute(): static
    {
        return $this->state(fn (array $attributes) => [
            'route_id' => Route::factory(),
        ]);
    }
}
