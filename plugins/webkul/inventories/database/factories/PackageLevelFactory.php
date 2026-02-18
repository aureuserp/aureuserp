<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\PackageLevel;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<PackageLevel>
 */
class PackageLevelFactory extends Factory
{
    protected $model = PackageLevel::class;

    public function definition(): array
    {
        return [
            'package_id'              => Package::factory(),
            'operation_id'            => Operation::factory(),
            'destination_location_id' => Location::factory(),
            'company_id'              => Company::factory(),
            'creator_id'              => User::factory(),
        ];
    }
}
