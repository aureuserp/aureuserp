<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Operation>
 */
class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        return [
            'name'                    => fake()->words(2, true),
            'origin'                  => null,
            'move_type'               => MoveType::DIRECT,
            'state'                   => OperationState::DRAFT,
            'is_favorite'             => false,
            'description'             => null,
            'has_deadline_issue'      => false,
            'is_printed'              => false,
            'is_locked'               => false,
            'deadline'                => null,
            'scheduled_at'            => now(),
            'closed_at'               => null,
            'user_id'                 => User::query()->value('id') ?? User::factory(),
            'owner_id'                => null,
            'operation_type_id'       => OperationType::factory(),
            'source_location_id'      => Location::factory(),
            'destination_location_id' => Location::factory(),
            'back_order_id'           => null,
            'return_id'               => null,
            'partner_id'              => null,
            'company_id'              => Company::factory(),
            'creator_id'              => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function receipt(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type_id'       => OperationType::factory()->receipt(),
            'source_location_id'      => Location::factory()->supplier(),
            'destination_location_id' => Location::factory()->internal(),
        ]);
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type_id'       => OperationType::factory()->internal(),
            'source_location_id'      => Location::factory()->internal(),
            'destination_location_id' => Location::factory()->internal(),
        ]);
    }

    public function delivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type_id'       => OperationType::factory()->delivery(),
            'source_location_id'      => Location::factory()->internal(),
            'destination_location_id' => Location::factory()->customer(),
        ]);
    }

    public function dropship(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type_id'       => OperationType::factory()->dropship(),
            'source_location_id'      => Location::factory()->supplier(),
            'destination_location_id' => Location::factory()->customer(),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OperationState::CONFIRMED,
        ]);
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'     => OperationState::DONE,
            'closed_at' => now(),
        ]);
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => Partner::query()->value('id') ?? Partner::factory(),
        ]);
    }

    public function withDeadline(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => now()->addDays(7),
        ]);
    }
}
