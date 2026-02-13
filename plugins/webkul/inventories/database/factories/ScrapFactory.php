<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Scrap;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<Scrap>
 */
class ScrapFactory extends Factory
{
    protected $model = Scrap::class;

    public function definition(): array
    {
        return [
            'name'             => fake()->numerify('SCRAP-######'),
            'origin'           => null,
            'state'            => ScrapState::DRAFT,
            'qty'              => 1,
            'should_replenish' => false,
            'closed_at'        => null,

            // Relationships
            'product_id'              => Product::factory(),
            'uom_id'                  => UOM::factory(),
            'lot_id'                  => null,
            'package_id'              => null,
            'partner_id'              => null,
            'operation_id'            => null,
            'source_location_id'      => Location::factory(),
            'destination_location_id' => Location::factory(),
            'company_id'              => Company::factory(),
            'creator_id'              => User::factory(),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'     => ScrapState::DONE,
            'closed_at' => now(),
        ]);
    }

    public function withLot(): static
    {
        return $this->state(fn (array $attributes) => [
            'lot_id' => Lot::factory(),
        ]);
    }

    public function withOperation(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_id' => Operation::factory(),
        ]);
    }
}
