<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<Move>
 */
class MoveFactory extends Factory
{
    protected $model = Move::class;

    public function definition(): array
    {
        return [
            'name'                => fake()->word(),
            'state'               => MoveState::DRAFT,
            'origin'              => null,
            'procure_method'      => ProcureMethod::MAKE_TO_STOCK,
            'reference'           => null,
            'description_picking' => null,
            'next_serial'         => null,
            'next_serial_count'   => 0,
            'is_favorite'         => false,
            'product_qty'         => 1,
            'product_uom_qty'     => 1,
            'quantity'            => 1,
            'is_picked'           => false,
            'is_scraped'          => false,
            'is_inventory'        => false,
            'is_refund'           => false,
            'deadline'            => null,
            'reservation_date'    => null,
            'scheduled_at'        => now(),

            // Relationships
            'product_id'              => Product::factory(),
            'uom_id'                  => UOM::factory(),
            'source_location_id'      => Location::factory(),
            'destination_location_id' => Location::factory(),
            'final_location_id'       => null,
            'partner_id'              => null,
            'operation_id'            => Operation::factory(),
            'rule_id'                 => null,
            'operation_type_id'       => OperationType::factory(),
            'origin_returned_move_id' => null,
            'restrict_partner_id'     => null,
            'warehouse_id'            => null,
            'product_packaging_id'    => null,
            'scrap_id'                => null,
            'company_id'              => Company::factory(),
            'creator_id'              => User::factory(),
            'purchase_order_line_id'  => null,
            'sale_order_line_id'      => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => MoveState::CONFIRMED,
        ]);
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => MoveState::DONE,
        ]);
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => Partner::factory(),
        ]);
    }

    public function withWarehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_id' => Warehouse::factory(),
        ]);
    }

    public function withRule(): static
    {
        return $this->state(fn (array $attributes) => [
            'rule_id' => Rule::factory(),
        ]);
    }
}
