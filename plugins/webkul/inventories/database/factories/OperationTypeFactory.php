<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;
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

            // Relationships
            'return_operation_type_id' => null,
            'source_location_id'       => Location::factory(),
            'destination_location_id'  => Location::factory(),
            'warehouse_id'             => Warehouse::factory(),
            'company_id'               => Company::factory(),
            'creator_id'               => User::factory(),
        ];
    }

    public function receipt(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => OperationTypeEnum::INCOMING,
        ]);
    }

    public function delivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => OperationTypeEnum::OUTGOING,
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
