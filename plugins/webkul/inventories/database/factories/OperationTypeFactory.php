<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<OperationType>
 */
class OperationTypeFactory extends Factory
{
    protected $model = OperationType::class;

    public function definition(): array
    {
        $company = Company::factory();

        return [
            'name'                               => fake()->words(2, true),
            'type'                               => OperationTypeEnum::INTERNAL,
            'sort'                               => 1,
            'sequence_code'                      => strtoupper(fake()->lexify('??')),
            'reservation_method'                 => ReservationMethod::AT_CONFIRM,
            'reservation_days_before'            => 0,
            'reservation_days_before_priority'   => 0,
            'product_label_format'               => null,
            'lot_label_format'                   => null,
            'package_label_to_print'             => null,
            'barcode'                            => null,
            'create_backorder'                   => CreateBackorder::ASK,
            'move_type'                          => MoveType::DIRECT,
            'show_entire_packs'                  => false,
            'use_create_lots'                    => false,
            'use_existing_lots'                  => false,
            'print_label'                        => false,
            'show_operations'                    => false,
            'auto_show_reception_report'         => false,
            'auto_print_delivery_slip'           => false,
            'auto_print_return_slip'             => false,
            'auto_print_product_labels'          => false,
            'auto_print_lot_labels'              => false,
            'auto_print_reception_report'        => false,
            'auto_print_reception_report_labels' => false,
            'auto_print_packages'                => false,
            'auto_print_package_label'           => false,
            'return_operation_type_id'           => null,
            'source_location_id'                 => Location::factory()->state(['company_id' => $company]),
            'destination_location_id'            => Location::factory()->state(['company_id' => $company]),
            'warehouse_id'                       => null,
            'company_id'                         => $company,
            'creator_id'                         => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function receipt(): static
    {
        $company = Company::factory();

        return $this->state(fn (array $attributes) => [
            'type'                    => OperationTypeEnum::INCOMING,
            'source_location_id'      => Location::factory()->supplier()->state(['company_id' => $company]),
            'destination_location_id' => Location::factory()->internal()->state(['company_id' => $company]),
            'company_id'              => $company,
            'warehouse_id'            => null,
        ]);
    }

    public function internal(): static
    {
        $company = Company::factory();

        return $this->state(fn (array $attributes) => [
            'type'                    => OperationTypeEnum::INTERNAL,
            'source_location_id'      => Location::factory()->internal()->state(['company_id' => $company]),
            'destination_location_id' => Location::factory()->internal()->state(['company_id' => $company]),
            'company_id'              => $company,
            'warehouse_id'            => null,
        ]);
    }

    public function delivery(): static
    {
        $company = Company::factory();

        return $this->state(fn (array $attributes) => [
            'type'                    => OperationTypeEnum::OUTGOING,
            'source_location_id'      => Location::factory()->internal()->state(['company_id' => $company]),
            'destination_location_id' => Location::factory()->customer()->state(['company_id' => $company]),
            'company_id'              => $company,
            'warehouse_id'            => null,
        ]);
    }

    public function dropship(): static
    {
        $company = Company::factory();

        return $this->state(fn (array $attributes) => [
            'type'                    => OperationTypeEnum::DROPSHIP,
            'source_location_id'      => Location::factory()->supplier()->state(['company_id' => $company]),
            'destination_location_id' => Location::factory()->customer()->state(['company_id' => $company]),
            'company_id'              => $company,
            'warehouse_id'            => null,
        ]);
    }

    public function withLotTracking(): static
    {
        return $this->state(fn (array $attributes) => [
            'use_create_lots'   => true,
            'use_existing_lots' => true,
        ]);
    }
}
