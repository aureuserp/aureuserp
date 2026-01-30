<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\ProductQuantityRelocation;
use Webkul\Security\Models\User;

/**
 * @extends Factory<ProductQuantityRelocation>
 */
class ProductQuantityRelocationFactory extends Factory
{
    protected $model = ProductQuantityRelocation::class;

    public function definition(): array
    {
        return [
            'description'             => null,
            'destination_location_id' => Location::factory(),
            'destination_package_id'  => null,
            'creator_id'              => User::factory(),
        ];
    }

    public function withDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->sentence(),
        ]);
    }

    public function withPackage(): static
    {
        return $this->state(fn (array $attributes) => [
            'destination_package_id' => Package::factory(),
        ]);
    }
}
