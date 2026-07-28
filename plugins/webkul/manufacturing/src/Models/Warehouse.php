<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse as BaseWarehouse;

class Warehouse extends BaseWarehouse
{
    protected array $manufactureRuleIds = [];

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'manufacture_to_resupply',
            'pbm_loc_id',
            'sam_loc_id',
            'manufacture_pull_id',
            'manufacture_mto_pull_id',
            'pbm_mto_pull_id',
            'sam_rule_id',
            'manu_type_id',
            'pbm_type_id',
            'sam_type_id',
            'pbm_route_id',
        ]);

        $this->mergeCasts([
            'manufacture_to_resupply' => 'boolean',
        ]);

        parent::__construct($attributes);
    }

    public function manufacturePull(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'manufacture_pull_id');
    }

    public function manufactureMtoPull(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'manufacture_mto_pull_id');
    }

    public function pbmMtoPull(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'pbm_mto_pull_id');
    }

    public function samRule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'sam_rule_id');
    }

    public function manuType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'manu_type_id');
    }

    public function pbmType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'pbm_type_id');
    }

    public function samType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'sam_type_id');
    }

    public function manufactureRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'pbm_route_id')->withTrashed();
    }

    public function pbmLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pbm_loc_id');
    }

    public function samLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'sam_loc_id');
    }

    public function suppliedWarehouses(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'inventories_warehouse_resupplies',
            'supplier_warehouse_id',
            'supplied_warehouse_id'
        );
    }

    public function supplierWarehouses(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'inventories_warehouse_resupplies',
            'supplied_warehouse_id',
            'supplier_warehouse_id'
        );
    }

    public function handleManufacturingWarehouseCreation(): void
    {
        $this->manufacture_steps ??= ManufactureStep::ONE_STEP;

        $this->createManufacturingLocations();

        $this->createManufacturingOperationTypes();

        $this->createManufacturingRoutes();

        $this->createManufacturingRules();

        $this->saveQuietly();
    }

    public function finalizeManufacturingWarehouseCreation(): void
    {
        Location::withTrashed()->whereIn('id', [
            $this->pbm_loc_id,
            $this->sam_loc_id,
        ])->update(['warehouse_id' => $this->id]);

        OperationType::withTrashed()->whereIn('id', [
            $this->pbm_type_id,
            $this->sam_type_id,
            $this->manu_type_id,
        ])->update(['warehouse_id' => $this->id]);

        $this->routes()->attach($this->pbm_route_id);

        Rule::withTrashed()->whereIn('id', $this->manufactureRuleIds)->update(['warehouse_id' => $this->id]);
    }

    public function createManufacturingLocations(): void
    {
        $this->pbm_loc_id = Location::create([
            'type'       => LocationType::INTERNAL,
            'name'       => 'Pre-Production',
            'barcode'    => $this->code.'PREPRODUCTION',
            'is_scrap'   => false,
            'parent_id'  => $this->view_location_id,
            'creator_id' => $this->creator_id,
            'company_id' => $this->company_id,
            'deleted_at' => in_array($this->manufacture_steps, [ManufactureStep::TWO_STEPS, ManufactureStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->sam_loc_id = Location::create([
            'type'       => LocationType::INTERNAL,
            'name'       => 'Post-Production',
            'barcode'    => $this->code.'POSTPRODUCTION',
            'is_scrap'   => false,
            'parent_id'  => $this->view_location_id,
            'creator_id' => $this->creator_id,
            'company_id' => $this->company_id,
            'deleted_at' => $this->manufacture_steps === ManufactureStep::THREE_STEPS ? null : now(),
        ])->id;
    }

    protected function createManufacturingOperationTypes(): void
    {
        $this->pbm_type_id = OperationType::create([
            'sort'                    => 18,
            'name'                    => 'Pick Components',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'PC',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code.'PC',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->lot_stock_location_id,
                ManufactureStep::TWO_STEPS   => $this->lot_stock_location_id,
                ManufactureStep::THREE_STEPS => $this->lot_stock_location_id,
            },
            'destination_location_id' => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->pbm_loc_id,
                ManufactureStep::TWO_STEPS   => $this->pbm_loc_id,
                ManufactureStep::THREE_STEPS => $this->pbm_loc_id,
            },
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => in_array($this->manufacture_steps, [ManufactureStep::TWO_STEPS, ManufactureStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->sam_type_id = OperationType::create([
            'sort'                    => 20,
            'name'                    => 'Store Finished Product',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'SFP',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code.'SFP',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->sam_loc_id,
                ManufactureStep::TWO_STEPS   => $this->sam_loc_id,
                ManufactureStep::THREE_STEPS => $this->sam_loc_id,
            },
            'destination_location_id' => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->lot_stock_location_id,
                ManufactureStep::TWO_STEPS   => $this->lot_stock_location_id,
                ManufactureStep::THREE_STEPS => $this->lot_stock_location_id,
            },
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => $this->manufacture_steps === ManufactureStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->manu_type_id = OperationType::create([
            'sort'                    => 19,
            'name'                    => 'Manufacturing',
            'type'                    => \Webkul\Inventory\Enums\OperationType::MANUFACTURE,
            'sequence_code'           => 'MO',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code.'MANUF',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->lot_stock_location_id,
                ManufactureStep::TWO_STEPS   => $this->pbm_loc_id,
                ManufactureStep::THREE_STEPS => $this->pbm_loc_id,
            },
            'destination_location_id' => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->lot_stock_location_id,
                ManufactureStep::TWO_STEPS   => $this->lot_stock_location_id,
                ManufactureStep::THREE_STEPS => $this->sam_loc_id,
            },
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
        ])->id;
    }

    protected function createManufacturingRoutes(): void
    {
        $this->pbm_route_id = Route::create([
            'name' => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->name.': Manufacture (1 step)',
                ManufactureStep::TWO_STEPS   => $this->name.': Pick components and then manufacture (2 steps)',
                ManufactureStep::THREE_STEPS => $this->name.': Pick components, manufacture and then store products (3 steps)',
            },
            'product_selectable'          => false,
            'product_category_selectable' => true,
            'warehouse_selectable'        => true,
            'packaging_selectable'        => false,
            'creator_id'                  => $this->creator_id,
            'company_id'                  => $this->company_id,
        ])->id;
    }

    protected function createManufacturingRules(): void
    {
        $productionLocation = Location::where('type', LocationType::PRODUCTION)->first();

        $this->manufactureRuleIds[] = Rule::create([
            'sort'                     => 15,
            'name'                     => $this->code.': Stock → Pre-Production',
            'route_sort'               => 10,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->lot_stock_location_id,
            'destination_location_id'  => $this->pbm_loc_id,
            'route_id'                 => $this->pbm_route_id,
            'operation_type_id'        => $this->pbm_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => in_array($this->manufacture_steps, [ManufactureStep::TWO_STEPS, ManufactureStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->manufactureRuleIds[] = Rule::create([
            'sort'                     => 16,
            'name'                     => $this->code.': Pre-Production → Production',
            'route_sort'               => 10,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->pbm_loc_id,
            'destination_location_id'  => $productionLocation->id,
            'route_id'                 => $this->pbm_route_id,
            'operation_type_id'        => $this->manu_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => in_array($this->manufacture_steps, [ManufactureStep::TWO_STEPS, ManufactureStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->manufactureRuleIds[] = Rule::create([
            'sort'                     => 17,
            'name'                     => $this->code.': Post-Production → Stock',
            'route_sort'               => 10,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->sam_loc_id,
            'destination_location_id'  => $this->lot_stock_location_id,
            'route_id'                 => $this->pbm_route_id,
            'operation_type_id'        => $this->sam_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->manufacture_steps === ManufactureStep::THREE_STEPS ? null : now(),
        ])->id;
    }

    public function syncManufacturingWarehouseConfiguration(): void
    {
        $this->updateLocations(
            'manufacture_steps',
            [
                ManufactureStep::ONE_STEP->value => [
                    'archive' => [$this->pbm_loc_id, $this->pbm_loc_id],
                ],
                ManufactureStep::TWO_STEPS->value => [
                    'restore' => [$this->pbm_loc_id],
                    'archive' => [$this->sam_loc_id],
                ],
                ManufactureStep::THREE_STEPS->value => [
                    'restore' => [$this->pbm_loc_id, $this->sam_loc_id],
                ],
            ]
        );

        $this->updateOperationTypes(
            'manufacture_steps',
            [
                ManufactureStep::ONE_STEP->value => [
                    'update' => [
                        $this->manu_type_id => [
                            'source_location_id'      => $this->lot_stock_location_id,
                            'destination_location_id' => $this->lot_stock_location_id,
                            'deleted_at'              => null,
                        ],
                    ],
                    'archive' => [$this->pbm_type_id, $this->sam_type_id],
                ],
                ManufactureStep::TWO_STEPS->value => [
                    'update' => [
                        $this->pbm_type_id => [
                            'source_location_id'      => $this->lot_stock_location_id,
                            'destination_location_id' => $this->pbm_loc_id,
                            'deleted_at'              => null,
                        ],
                        $this->manu_type_id => [
                            'source_location_id'      => $this->pbm_loc_id,
                            'destination_location_id' => $this->lot_stock_location_id,
                            'deleted_at'              => null,
                        ],
                    ],
                    'archive' => [$this->sam_type_id],
                ],
                ManufactureStep::THREE_STEPS->value => [
                    'update' => [
                        $this->pbm_type_id => [
                            'source_location_id'      => $this->lot_stock_location_id,
                            'destination_location_id' => $this->pbm_loc_id,
                            'deleted_at'              => null,
                        ],
                        $this->manu_type_id => [
                            'source_location_id'      => $this->pbm_loc_id,
                            'destination_location_id' => $this->sam_loc_id,
                            'deleted_at'              => null,
                        ],
                        $this->sam_type_id => [
                            'source_location_id'      => $this->sam_loc_id,
                            'destination_location_id' => $this->lot_stock_location_id,
                            'deleted_at'              => null,
                        ],
                    ],
                ],
            ]
        );

        $this->manufactureRoute?->update([
            'name' => match ($this->manufacture_steps) {
                ManufactureStep::ONE_STEP    => $this->name.': Manufacture (1 step)',
                ManufactureStep::TWO_STEPS   => $this->name.': Pick components and then manufacture (2 steps)',
                ManufactureStep::THREE_STEPS => $this->name.': Pick components, manufacture and then store products (3 steps)',
            },
            'deleted_at' => $this->manufacture_steps === ManufactureStep::ONE_STEP ? now() : null,
        ]);

        $productionLocation = Location::where('type', LocationType::PRODUCTION)->first();

        $this->updateRules(
            'manufacture_steps',
            [
                ManufactureStep::ONE_STEP->value => [
                    'archive' => [
                        // WH: Stock → Pre-Production => WH/Stock → WH/Pre-Production
                        ['source_location_id' => $this->lot_stock_location_id, 'destination_location_id' => $this->pbm_loc_id, 'operation_type_id' => $this->pbm_type_id],
                        // WH: Pre-Production → Production => WH/Pre-Production → Virtual Locations/Production
                        ['source_location_id' => $this->pbm_loc_id, 'destination_location_id' => $productionLocation->id, 'operation_type_id' => $this->manu_type_id],
                        // WH: Post-Production → Stock => WH/Post-Production → WH/Stock
                        ['source_location_id' => $this->sam_loc_id, 'destination_location_id' => $this->lot_stock_location_id, 'operation_type_id' => $this->sam_type_id],
                    ],
                ],
                ManufactureStep::TWO_STEPS->value => [
                    'restore' => [
                        // WH: Stock → Pre-Production => WH/Stock → WH/Pre-Production
                        ['source_location_id' => $this->lot_stock_location_id, 'destination_location_id' => $this->pbm_loc_id, 'operation_type_id' => $this->pbm_type_id],
                        // WH: Pre-Production → Production => WH/Pre-Production → Virtual Locations/Production
                        ['source_location_id' => $this->pbm_loc_id, 'destination_location_id' => $productionLocation->id, 'operation_type_id' => $this->manu_type_id],
                    ],
                    'archive' => [
                        // WH: Post-Production → Stock => WH/Post-Production → WH/Stock
                        ['source_location_id' => $this->sam_loc_id, 'destination_location_id' => $this->lot_stock_location_id, 'operation_type_id' => $this->sam_type_id],
                    ],
                ],
                ManufactureStep::THREE_STEPS->value => [
                    'restore' => [
                        // WH: Stock → Pre-Production => WH/Stock → WH/Pre-Production
                        ['source_location_id' => $this->lot_stock_location_id, 'destination_location_id' => $this->pbm_loc_id, 'operation_type_id' => $this->pbm_type_id],
                        // WH: Pre-Production → Production => WH/Pre-Production → Virtual Locations/Production
                        ['source_location_id' => $this->pbm_loc_id, 'destination_location_id' => $productionLocation->id, 'operation_type_id' => $this->manu_type_id],
                        // WH: Post-Production → Stock => WH/Post-Production → WH/Stock
                        ['source_location_id' => $this->sam_loc_id, 'destination_location_id' => $this->lot_stock_location_id, 'operation_type_id' => $this->sam_type_id],
                    ],
                ],
            ]
        );
    }
}
