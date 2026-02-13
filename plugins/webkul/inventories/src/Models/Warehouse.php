<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Inventory\Database\Factories\WarehouseFactory;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Warehouse extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'inventories_warehouses';

    protected array $routeIds = [];

    protected $fillable = [
        'name',
        'code',
        'sort',
        'reception_steps',
        'delivery_steps',
        'partner_address_id',
        'company_id',
        'creator_id',
        'view_location_id',
        'lot_stock_location_id',
        'input_stock_location_id',
        'qc_stock_location_id',
        'output_stock_location_id',
        'pack_stock_location_id',
        'mto_pull_id',
        'buy_pull_id',
        'pick_type_id',
        'pack_type_id',
        'out_type_id',
        'in_type_id',
        'internal_type_id',
        'qc_type_id',
        'store_type_id',
        'xdock_type_id',
        'crossdock_route_id',
        'reception_route_id',
        'delivery_route_id',
    ];

    protected $casts = [
        'reception_steps' => ReceptionStep::class,
        'delivery_steps'  => DeliveryStep::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function partnerAddress(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function viewLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'view_location_id');
    }

    public function lotStockLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'lot_stock_location_id');
    }

    public function inputStockLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'input_stock_location_id');
    }

    public function qcStockLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'qc_stock_location_id');
    }

    public function outputStockLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'output_stock_location_id');
    }

    public function packStockLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pack_stock_location_id');
    }

    public function mtoPull(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'mto_pull_id');
    }

    public function buyPull(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'buy_pull_id');
    }

    public function pickType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'pick_type_id');
    }

    public function packType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'pack_type_id');
    }

    public function outType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'out_type_id');
    }

    public function inType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'in_type_id');
    }

    public function internalType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'internal_type_id');
    }

    public function qcType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'qc_type_id');
    }

    public function storeType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'store_type_id');
    }

    public function xdockType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'xdock_type_id');
    }

    public function crossdockRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'crossdock_route_id');
    }

    public function receptionRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'reception_route_id');
    }

    public function deliveryRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'delivery_route_id');
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_route_warehouses', 'warehouse_id', 'route_id');
    }

    public function suppliedWarehouses(): BelongsToMany
    {
        return $this->belongsToMany(
            Warehouse::class,
            'inventories_warehouse_resupplies',
            'supplier_warehouse_id',
            'supplied_warehouse_id'
        );
    }

    public function supplierWarehouses(): BelongsToMany
    {
        return $this->belongsToMany(
            Warehouse::class,
            'inventories_warehouse_resupplies',
            'supplied_warehouse_id',
            'supplier_warehouse_id'
        );
    }

    protected function handleWarehouseCreation(): void
    {
        $this->creator_id ??= Auth::id();

        $this->company_id ??= Auth::user()?->default_company_id;

        $this->reception_steps ??= ReceptionStep::ONE_STEP;

        $this->delivery_steps ??= DeliveryStep::ONE_STEP;

        $this->createLocations();

        $this->createOperationTypes();

        $this->createRoutes();

        $this->createRules();
    }

    protected function createLocations(): void
    {
        $this->view_location_id = Location::create([
            'type'         => LocationType::VIEW,
            'name'         => $this->code,
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => 1,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
        ])->id;

        $this->lot_stock_location_id = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Stock',
            'barcode'      => $this->code . 'STOCK',
            'is_scrap'     => false,
            'is_replenish' => true,
            'parent_id'    => $this->view_location_id,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
        ])->id;

        $this->input_stock_location_id = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Input',
            'barcode'      => $this->code . 'INPUT',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $this->view_location_id,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
            'deleted_at'   => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->qc_stock_location_id = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Quality Control',
            'barcode'      => $this->code . 'QUALITY',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $this->view_location_id,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
            'deleted_at'   => $this->reception_steps === ReceptionStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->output_stock_location_id = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Output',
            'barcode'      => $this->code . 'OUTPUT',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $this->view_location_id,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
            'deleted_at'   => in_array($this->delivery_steps, [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->pack_stock_location_id = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Packing Zone',
            'barcode'      => $this->code . 'PACKING',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $this->view_location_id,
            'creator_id'   => $this->creator_id,
            'company_id'   => $this->company_id,
            'deleted_at'   => $this->delivery_steps === DeliveryStep::THREE_STEPS ? null : now(),
        ])->id;
    }

    protected function createOperationTypes(): void
    {
        $supplierLocation = Location::where('type', LocationType::SUPPLIER)->first();

        $customerLocation = Location::where('type', LocationType::CUSTOMER)->first();

        $this->in_type_id = OperationType::create([
            'sort'                    => 1,
            'name'                    => 'Receipts',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INCOMING,
            'sequence_code'           => 'IN',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'IN',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $supplierLocation->id,
            'destination_location_id' => match ($this->reception_steps) {
                ReceptionStep::ONE_STEP    => $this->lot_stock_location_id,
                ReceptionStep::TWO_STEPS   => $this->input_stock_location_id,
                ReceptionStep::THREE_STEPS => $this->input_stock_location_id,
            },
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
        ])->id;

        $this->out_type_id = OperationType::create([
            'sort'                    => 2,
            'name'                    => 'Delivery Orders',
            'type'                    => \Webkul\Inventory\Enums\OperationType::OUTGOING,
            'sequence_code'           => 'OUT',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'OUT',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => true,
            'show_operations'         => false,
            'source_location_id'      => match ($this->reception_steps) {
                ReceptionStep::ONE_STEP    => $this->lot_stock_location_id,
                ReceptionStep::TWO_STEPS   => $this->output_stock_location_id,
                ReceptionStep::THREE_STEPS => $this->output_stock_location_id,
            },
            'destination_location_id' => $customerLocation->id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
        ])->id;

        $this->pick_type_id = OperationType::create([
            'sort'                    => 3,
            'name'                    => 'Pick',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'PICK',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'PICK',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $this->lot_stock_location_id,
            'destination_location_id' => match ($this->delivery_steps) {
                DeliveryStep::ONE_STEP    => $this->pack_stock_location_id,
                DeliveryStep::TWO_STEPS   => $this->output_stock_location_id,
                DeliveryStep::THREE_STEPS => $this->pack_stock_location_id,
            },
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => $this->delivery_steps === DeliveryStep::ONE_STEP ? now() : null,
        ])->id;

        $this->pack_type_id = OperationType::create([
            'sort'                    => 4,
            'name'                    => 'Pack',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'PACK',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'PACK',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $this->pack_stock_location_id,
            'destination_location_id' => $this->output_stock_location_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => $this->delivery_steps !== DeliveryStep::THREE_STEPS ? now() : null,
        ])->id;

        $this->qc_type_id = OperationType::create([
            'sort'                    => 5,
            'name'                    => 'Quality Control',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'QC',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'QC',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $this->input_stock_location_id,
            'destination_location_id' => $this->qc_stock_location_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => $this->reception_steps !== ReceptionStep::THREE_STEPS ? now() : null,
        ])->id;

        $this->store_type_id = OperationType::create([
            'sort'                    => 6,
            'name'                    => 'Storage',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'STOR',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'STOR',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => match ($this->reception_steps) {
                ReceptionStep::ONE_STEP    => $this->input_stock_location_id,
                ReceptionStep::TWO_STEPS   => $this->input_stock_location_id,
                ReceptionStep::THREE_STEPS => $this->qc_stock_location_id,
            },
            'destination_location_id' => $this->lot_stock_location_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->internal_type_id = OperationType::create([
            'sort'                    => 7,
            'name'                    => 'Internal Transfers',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'INT',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'INT',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $this->lot_stock_location_id,
            'destination_location_id' => $this->lot_stock_location_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => app(\Webkul\Inventory\Settings\WarehouseSettings::class)->enable_locations ? null : now(),
        ])->id;

        $this->xdock_type_id = OperationType::create([
            'sort'                    => 8,
            'name'                    => 'Cross Dock',
            'type'                    => \Webkul\Inventory\Enums\OperationType::INTERNAL,
            'sequence_code'           => 'XD',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $this->code . 'XD',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $this->input_stock_location_id,
            'destination_location_id' => $this->output_stock_location_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'deleted_at'              => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($this->delivery_steps, [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;
    }

    protected function createRoutes(): void
    {
        $this->reception_route_id = Route::create([
            'name' => match ($this->reception_steps) {
                ReceptionStep::ONE_STEP    => $this->name . ': Receive in 1 step (Stock)',
                ReceptionStep::TWO_STEPS   => $this->name . ': Receive in 2 steps (Input + Stock)',
                ReceptionStep::THREE_STEPS => $this->name . ': Receive in 3 steps (Input + Quality + Stock)',
            },
            'product_selectable'          => false,
            'product_category_selectable' => true,
            'warehouse_selectable'        => true,
            'packaging_selectable'        => false,
            'creator_id'                  => $this->creator_id,
            'company_id'                  => $this->company_id,
        ])->id;

        $this->delivery_route_id = Route::create([
            'name' => match ($this->delivery_steps) {
                DeliveryStep::ONE_STEP    => $this->name . ': Deliver in 1 step (Ship)',
                DeliveryStep::TWO_STEPS   => $this->name . ': Deliver in 2 steps (Pick + Ship)',
                DeliveryStep::THREE_STEPS => $this->name . ': Deliver in 3 steps (Pick + Pack + Ship)',
            },
            'product_selectable'          => false,
            'product_category_selectable' => true,
            'warehouse_selectable'        => true,
            'packaging_selectable'        => false,
            'creator_id'                  => $this->creator_id,
            'company_id'                  => $this->company_id,
        ])->id;

        $this->crossdock_route_id = Route::create([
            'name'                        => $this->name . ': Cross-Dock',
            'product_selectable'          => true,
            'product_category_selectable' => true,
            'warehouse_selectable'        => false,
            'packaging_selectable'        => false,
            'creator_id'                  => $this->creator_id,
            'company_id'                  => $this->company_id,
            'deleted_at'                  => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($this->delivery_steps, [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;
    }

    protected function createRules(): void
    {
        $supplierLocation = Location::where('type', LocationType::SUPPLIER)->first();

        $customerLocation = Location::where('type', LocationType::CUSTOMER)->first();

        $this->routeIds[] = Rule::create([
            'sort'                     => 1,
            'name'                     => $this->code . ': Vendors → Stock',
            'route_sort'               => 9,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $supplierLocation->id,
            'destination_location_id'  => $this->lot_stock_location_id,
            'route_id'                 => $this->reception_route_id,
            'operation_type_id'        => $this->in_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                      => 2,
            'name'                      => $this->code . ': Stock → Customers',
            'route_sort'                => 10,
            'group_propagation_option'  => GroupPropagation::PROPAGATE,
            'action'                    => RuleAction::PULL,
            'procure_method'            => ProcureMethod::MAKE_TO_STOCK,
            'auto'                      => RuleAuto::MANUAL,
            'propagate_cancel'          => false,
            'propagate_carrier'         => true,
            'source_location_id'        => $this->lot_stock_location_id,
            'destination_location_id'   => $customerLocation->id,
            'route_id'                  => $this->delivery_route_id,
            'operation_type_id'         => $this->out_type_id,
            'creator_id'                => $this->creator_id,
            'company_id'                => $this->company_id,
            'deleted_at'                => $this->delivery_steps === DeliveryStep::ONE_STEP ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 3,
            'name'                     => $this->code . ': Vendors → Customers',
            'route_sort'               => 20,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $supplierLocation->id,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $this->crossdock_route_id,
            'operation_type_id'        => $this->in_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($this->delivery_steps, [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 4,
            'name'                     => $this->code . ': Input → Output',
            'route_sort'               => 20,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->input_stock_location_id,
            'destination_location_id'  => $this->output_stock_location_id,
            'route_id'                 => $this->crossdock_route_id,
            'operation_type_id'        => $this->xdock_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($this->delivery_steps, [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->routeIds[] = $this->mto_pull_id = Rule::create([
            'sort'                     => 5,
            'name'                     => $this->code . ': Stock → Customers (MTO)',
            'route_sort'               => 5,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $this->lot_stock_location_id,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => 1,
            'operation_type_id'        => $this->out_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 6,
            'name'                     => $this->code . ': Input → Quality Control',
            'route_sort'               => 6,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => true,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->input_stock_location_id,
            'destination_location_id'  => $this->qc_stock_location_id,
            'route_id'                 => $this->reception_route_id,
            'operation_type_id'        => $this->qc_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->reception_steps === ReceptionStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 7,
            'name'                     => $this->code . ': Quality Control → Stock',
            'route_sort'               => 7,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->qc_stock_location_id,
            'destination_location_id'  => $this->lot_stock_location_id,
            'route_id'                 => $this->reception_route_id,
            'operation_type_id'        => $this->store_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->reception_steps === ReceptionStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 8,
            'name'                     => $this->code . ': Stock → Customers',
            'route_sort'               => 8,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $this->lot_stock_location_id,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $this->delivery_route_id,
            'operation_type_id'        => $this->pick_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->delivery_steps === DeliveryStep::ONE_STEP ? now() : null,
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 9,
            'name'                     => $this->code . ': Packing Zone → Output',
            'route_sort'               => 9,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $this->pack_stock_location_id,
            'destination_location_id'  => $this->output_stock_location_id,
            'route_id'                 => $this->delivery_route_id,
            'operation_type_id'        => $this->pack_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->delivery_steps === DeliveryStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 10,
            'name'                     => $this->code . ': Output → Customers',
            'route_sort'               => 10,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $this->output_stock_location_id,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $this->delivery_route_id,
            'operation_type_id'        => $this->out_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->delivery_steps === DeliveryStep::ONE_STEP ? now() : null,
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 11,
            'name'                     => $this->code . ': Input → Stock',
            'route_sort'               => 11,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $this->input_stock_location_id,
            'destination_location_id'  => $this->lot_stock_location_id,
            'route_id'                 => $this->reception_route_id,
            'operation_type_id'        => $this->store_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => $this->delivery_steps === ReceptionStep::TWO_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 12,
            'name'                     => $this->code . ': False → Customers',
            'route_sort'               => 12,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::BUY,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => null,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $this->crossdock_route_id,
            'operation_type_id'        => $this->in_type_id,
            'creator_id'               => $this->creator_id,
            'company_id'               => $this->company_id,
            'deleted_at'               => in_array($this->reception_steps, [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($this->delivery_steps, [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;
    }

    protected static function newFactory(): WarehouseFactory
    {
        return WarehouseFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Warehouse $warehouse) {
            $warehouse->handleWarehouseCreation();
        });

        static::updated(function (Warehouse $warehouse) {
            if ($warehouse->wasChanged('code')) {
                $warehouse->viewLocation->update(['name' => $warehouse->code]);
            }
        });
    }
}
