<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Inventory\Models\StorageCategoryCapacity;
use Webkul\Security\Models\User;

/**
 * @extends Factory<StorageCategoryCapacity>
 */
class StorageCategoryCapacityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StorageCategoryCapacity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'qty'                 => 10.0,
            'product_id'          => null,
            'storage_category_id' => StorageCategory::factory(),
            'package_type_id'     => null,
            'creator_id'          => User::factory(),
        ];
    }

    public function forProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => \Webkul\Product\Models\Product::factory(),
        ]);
    }

    public function forPackageType(): static
    {
        return $this->state(fn (array $attributes) => [
            'package_type_id' => PackageType::factory(),
        ]);
    }
}
