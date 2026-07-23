<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        return [
            'name'                   => fake()->words(2, true),
            'barcode'                => strtoupper(fake()->bothify('WO-######')),
            'production_availability'=> null,
            'state'                  => WorkOrderState::PENDING,
            'quantity_produced'      => 0,
            'expected_duration'      => fake()->randomFloat(4, 1, 120),
            'started_at'             => null,
            'finished_at'            => null,
            'duration'               => null,
            'duration_per_unit'      => null,
            'costs_per_hour'         => null,
            'work_center_id'         => WorkCenter::factory(),
            'product_id'             => Product::query()->value('id') ?? Product::factory(),
            'uom_id'                 => UOM::query()->value('id') ?? UOM::factory(),
            'manufacturing_order_id' => Order::factory(),
            'calendar_leave_id'      => null,
            'operation_id'           => Operation::factory(),
            'creator_id'             => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['state' => WorkOrderState::PENDING]);
    }

    public function ready(): static
    {
        return $this->state(fn () => ['state' => WorkOrderState::READY]);
    }

    public function progress(): static
    {
        return $this->state(fn () => ['state' => WorkOrderState::PROGRESS]);
    }

    public function done(): static
    {
        return $this->state(fn () => ['state' => WorkOrderState::DONE]);
    }

    public function expectedDuration(float $minutes): static
    {
        return $this->state(fn () => ['expected_duration' => $minutes]);
    }
}
