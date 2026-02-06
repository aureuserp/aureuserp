<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\PackageType;
use Webkul\Security\Models\User;

/**
 * @extends Factory<PackageType>
 */
class PackageTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PackageType::class;

    public function definition(): array
    {
        return [
            'name'                 => fake()->word(),
            'sort'                 => 0,
            'barcode'              => null,
            'height'               => null,
            'width'                => null,
            'length'               => null,
            'base_weight'          => null,
            'max_weight'           => null,
            'shipper_package_code' => null,
            'package_carrier_type' => null,

            // Relationships
            'company_id' => \Webkul\Support\Models\Company::factory(),
            'creator_id' => User::factory(),
        ];
    }

    public function withDimensions(): static
    {
        return $this->state(fn (array $attributes) => [
            'height'      => fake()->randomFloat(2, 10, 100),
            'width'       => fake()->randomFloat(2, 10, 100),
            'length'      => fake()->randomFloat(2, 10, 100),
            'base_weight' => fake()->randomFloat(2, 0.1, 5),
            'max_weight'  => fake()->randomFloat(2, 1, 50),
        ]);
    }
}
