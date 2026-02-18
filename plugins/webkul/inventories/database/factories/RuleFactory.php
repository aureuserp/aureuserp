<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Rule>
 */
class RuleFactory extends Factory
{
    protected $model = Rule::class;

    public function definition(): array
    {
        return [
            'name'                     => fake()->words(2, true),
            'sort'                     => 1,
            'route_sort'               => 1,
            'delay'                    => 0,
            'group_propagation_option' => null,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => true,
            'push_domain'              => null,
            'location_dest_from_rule'  => false,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,

            // Relationships
            'source_location_id'      => Location::factory(),
            'destination_location_id' => Location::factory(),
            'route_id'                => Route::factory(),
            'operation_type_id'       => OperationType::factory(),
            'partner_address_id'      => null,
            'warehouse_id'            => null,
            'propagate_warehouse_id'  => null,
            'company_id'              => Company::factory(),
            'creator_id'              => User::factory(),
        ];
    }

    public function push(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => RuleAction::PUSH,
        ]);
    }

    public function withWarehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_id' => Warehouse::factory(),
        ]);
    }
}
