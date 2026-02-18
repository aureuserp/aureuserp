<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Package;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<MoveLine>
 */
class MoveLineFactory extends Factory
{
    protected $model = MoveLine::class;

    public function definition(): array
    {
        return [
            'lot_name'            => null,
            'state'               => MoveState::DRAFT,
            'reference'           => null,
            'picking_description' => null,
            'qty'                 => 1,
            'uom_qty'             => 1,
            'is_picked'           => false,
            'scheduled_at'        => now(),

            // Relationships
            'move_id'                 => Move::factory(),
            'operation_id'            => Operation::factory(),
            'product_id'              => Product::factory(),
            'uom_id'                  => UOM::factory(),
            'package_id'              => null,
            'result_package_id'       => null,
            'package_level_id'        => null,
            'lot_id'                  => null,
            'partner_id'              => null,
            'source_location_id'      => Location::factory(),
            'destination_location_id' => Location::factory(),
            'company_id'              => Company::factory(),
            'creator_id'              => User::factory(),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'     => MoveState::DONE,
            'is_picked' => true,
        ]);
    }

    public function withLot(): static
    {
        return $this->state(fn (array $attributes) => [
            'lot_id'   => Lot::factory(),
            'lot_name' => fake()->word(),
        ]);
    }

    public function withPackage(): static
    {
        return $this->state(fn (array $attributes) => [
            'package_id' => Package::factory(),
        ]);
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => Partner::factory(),
        ]);
    }
}
