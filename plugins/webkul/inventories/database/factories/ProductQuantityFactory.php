<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<ProductQuantity>
 */
class ProductQuantityFactory extends Factory
{
    protected $model = ProductQuantity::class;

    public function definition(): array
    {
        return [
            'quantity'                => 100.0,
            'reserved_quantity'       => 0.0,
            'counted_quantity'        => null,
            'difference_quantity'     => 0.0,
            'inventory_diff_quantity' => 0.0,
            'inventory_quantity_set'  => false,
            'scheduled_at'            => null,
            'incoming_at'             => null,

            // Relationships
            'product_id'          => Product::factory(),
            'location_id'         => Location::factory(),
            'storage_category_id' => null,
            'lot_id'              => null,
            'package_id'          => null,
            'partner_id'          => null,
            'user_id'             => null,
            'company_id'          => Company::factory(),
            'creator_id'          => User::factory(),
        ];
    }

    public function withLot(): static
    {
        return $this->state(fn (array $attributes) => [
            'lot_id' => Lot::factory(),
        ]);
    }

    public function withPackage(): static
    {
        return $this->state(fn (array $attributes) => [
            'package_id' => Package::factory(),
        ]);
    }

    public function withStorageCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'storage_category_id' => StorageCategory::factory(),
        ]);
    }
}
