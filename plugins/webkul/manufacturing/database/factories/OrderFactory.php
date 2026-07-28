<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\ManufacturingOrderPriority;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Order;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'reference'               => strtoupper(fake()->bothify('MO-######')),
            'priority'                => ManufacturingOrderPriority::NORMAL,
            'origin'                  => fake()->optional()->sentence(3),
            'state'                   => ManufacturingOrderState::DRAFT,
            'reservation_state'       => null,
            'consumption'             => BillOfMaterialConsumption::WARNING,
            'quantity'                => fake()->randomFloat(4, 1, 50),
            'quantity_producing'      => 0,
            'deadline_at'             => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'started_at'              => fake()->dateTimeBetween('-1 week', 'now'),
            'finished_at'             => null,
            'production_location_id'  => null,
            'procurement_group_id'    => null,
            'product_id'              => Product::query()->value('id') ?? Product::factory(),
            'uom_id'                  => UOM::query()->value('id') ?? UOM::factory(),
            'producing_lot_id'        => Lot::query()->value('id'),
            'operation_type_id'       => OperationType::query()->value('id') ?? OperationType::factory(),
            'source_location_id'      => Location::query()->value('id') ?? Location::factory(),
            'destination_location_id' => Location::query()->value('id') ?? Location::factory(),
            'final_location_id'       => null,
            'bill_of_material_id'     => BillOfMaterial::factory(),
            'assigned_user_id'        => User::query()->value('id'),
            'company_id'              => Company::query()->value('id') ?? Company::factory(),
            'order_point_id'          => OrderPoint::query()->value('id'),
            'creator_id'              => User::query()->value('id') ?? User::factory(),
        ];
    }
}
