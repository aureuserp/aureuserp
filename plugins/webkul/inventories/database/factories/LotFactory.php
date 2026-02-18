<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<Lot>
 */
class LotFactory extends Factory
{
    protected $model = Lot::class;

    public function definition(): array
    {
        return [
            'name'            => fake()->unique()->numerify('LOT-######'),
            'description'     => null,
            'reference'       => null,
            'properties'      => null,
            'expiry_reminded' => false,
            'expiration_date' => null,
            'use_date'        => null,
            'removal_date'    => null,
            'alert_date'      => null,

            // Relationships
            'product_id'  => Product::factory(),
            'uom_id'      => UOM::factory(),
            'location_id' => null,
            'company_id'  => Company::factory(),
            'creator_id'  => User::factory(),
        ];
    }

    public function withExpiration(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiration_date' => now()->addYear(),
            'use_date'        => now()->addMonths(11),
            'removal_date'    => now()->addYear(),
            'alert_date'      => now()->addMonths(11),
        ]);
    }

    public function withLocation(): static
    {
        return $this->state(fn (array $attributes) => [
            'location_id' => Location::factory(),
        ]);
    }
}
