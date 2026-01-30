<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\PackageType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->numerify('PKG-######'),
            'package_use' => PackageUse::REUSABLE,
            'pack_date'   => now(),

            // Relationships
            'package_type_id' => PackageType::factory(),
            'location_id'     => Location::factory(),
            'company_id'      => Company::factory(),
            'creator_id'      => User::factory(),
        ];
    }

    public function disposable(): static
    {
        return $this->state(fn (array $attributes) => [
            'package_use' => PackageUse::DISPOSABLE,
        ]);
    }
}
