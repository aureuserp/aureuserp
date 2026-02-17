<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        return [
            'name'                        => fake()->words(2, true),
            'sort'                        => 1,
            'product_selectable'          => false,
            'product_category_selectable' => false,
            'warehouse_selectable'        => false,
            'packaging_selectable'        => false,

            // Relationships
            'supplied_warehouse_id' => null,
            'supplier_warehouse_id' => null,
            'company_id'            => Company::factory(),
            'creator_id'            => User::factory(),
        ];
    }

    public function withWarehouses(): static
    {
        return $this->state(fn (array $attributes) => [
            'supplied_warehouse_id' => Warehouse::factory(),
            'supplier_warehouse_id' => Warehouse::factory(),
        ]);
    }
}
