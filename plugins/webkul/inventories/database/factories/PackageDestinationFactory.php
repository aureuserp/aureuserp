<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\PackageDestination;
use Webkul\Security\Models\User;

/**
 * @extends Factory<PackageDestination>
 */
class PackageDestinationFactory extends Factory
{
    protected $model = PackageDestination::class;

    public function definition(): array
    {
        return [
            'operation_id'            => Operation::factory(),
            'destination_location_id' => Location::factory(),
            'creator_id'              => User::factory(),
        ];
    }
}
