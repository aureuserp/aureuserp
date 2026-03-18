<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Enums\ProductRemoval;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name'                       => fake()->words(2, true),
            'full_name'                  => fake()->words(2, true),
            'type'                       => LocationType::INTERNAL,
            'description'                => null,
            'parent_path'                => null,
            'barcode'                    => null,
            'removal_strategy'           => ProductRemoval::FIFO,
            'cyclic_inventory_frequency' => 0,
            'last_inventory_date'        => null,
            'next_inventory_date'        => null,
            'is_scrap'                   => false,
            'is_replenish'               => false,
            'is_dock'                    => false,
            'position_x'                 => null,
            'position_y'                 => null,
            'position_z'                 => null,
            'parent_id'                  => null,
            'storage_category_id'        => null,
            'warehouse_id'               => null,
            'company_id'                 => Company::factory(),
            'creator_id'                 => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::SUPPLIER,
        ]);
    }

    public function view(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::VIEW,
        ]);
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::INTERNAL,
        ]);
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::CUSTOMER,
        ]);
    }

    public function inventory(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::INVENTORY,
        ]);
    }

    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::PRODUCTION,
        ]);
    }

    public function transit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::TRANSIT,
        ]);
    }

    public function scrap(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'     => LocationType::INVENTORY,
            'is_scrap' => true,
        ]);
    }

    public function withWarehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_id' => Warehouse::factory(),
        ]);
    }

    public function withStorageCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'storage_category_id' => StorageCategory::factory(),
        ]);
    }

    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Location::factory(),
        ]);
    }
}
