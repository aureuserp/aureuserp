<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name'            => fake()->company(),
            'code'            => strtoupper(fake()->lexify('WH???')),
            'sort'            => 1,
            'reception_steps' => ReceptionStep::ONE_STEP,
            'delivery_steps'  => DeliveryStep::ONE_STEP,

            // Relationships
            'partner_address_id'       => null,
            'company_id'               => Company::factory(),
            'creator_id'               => User::factory(),
            'view_location_id'         => null,
            'lot_stock_location_id'    => null,
            'input_stock_location_id'  => null,
            'qc_stock_location_id'     => null,
            'output_stock_location_id' => null,
            'pack_stock_location_id'   => null,
            'mto_pull_id'              => null,
            'buy_pull_id'              => null,
            'pick_type_id'             => null,
            'pack_type_id'             => null,
            'out_type_id'              => null,
            'in_type_id'               => null,
            'internal_type_id'         => null,
            'qc_type_id'               => null,
            'store_type_id'            => null,
            'xdock_type_id'            => null,
            'crossdock_route_id'       => null,
            'reception_route_id'       => null,
            'delivery_route_id'        => null,
        ];
    }

    public function withAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_address_id' => Partner::factory(),
        ]);
    }

    public function twoStepReception(): static
    {
        return $this->state(fn (array $attributes) => [
            'reception_steps' => ReceptionStep::TWO_STEPS,
        ]);
    }

    public function pickAndShip(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_steps' => DeliveryStep::TWO_STEPS,
        ]);
    }
}
