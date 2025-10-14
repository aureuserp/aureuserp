<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums;
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
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Settings\WarehouseSettings;

class CreateWarehouse extends CreateRecord
{
    protected static string $resource = WarehouseResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected array $routeIds = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/create-warehouse.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/create-warehouse.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        $data['company_id'] = $data['company_id'] ?? Auth::user()->default_company_id;

        $data['reception_steps'] ??= ReceptionStep::ONE_STEP;

        $data['delivery_steps'] ??= DeliveryStep::ONE_STEP;

        $data = $this->createLocations($data);

        $data = $this->createOperationTypes($data);

        $data = $this->createRoutes($data);

        $data = $this->createRules($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        Location::withTrashed()->whereIn('id', [
            $this->getRecord()->view_location_id,
            $this->getRecord()->lot_stock_location_id,
            $this->getRecord()->input_stock_location_id,
            $this->getRecord()->qc_stock_location_id,
            $this->getRecord()->output_stock_location_id,
            $this->getRecord()->pack_stock_location_id,
        ])->update(['warehouse_id' => $this->getRecord()->id]);

        OperationType::withTrashed()->whereIn('id', [
            $this->getRecord()->in_type_id,
            $this->getRecord()->out_type_id,
            $this->getRecord()->pick_type_id,
            $this->getRecord()->pack_type_id,
            $this->getRecord()->qc_type_id,
            $this->getRecord()->store_type_id,
            $this->getRecord()->internal_type_id,
            $this->getRecord()->xdock_type_id,
        ])->update(['warehouse_id' => $this->getRecord()->id]);

        $this->getRecord()->routes()->sync([
            $this->getRecord()->reception_route_id,
            $this->getRecord()->delivery_route_id,
            $this->getRecord()->crossdock_route_id,
        ]);

        Rule::withTrashed()->whereIn('id', $this->routeIds)->update(['warehouse_id' => $this->getRecord()->id]);
    }

    protected function createLocations(array $data): array
    {
        $data['view_location_id'] = Location::create([
            'type'         => LocationType::VIEW,
            'name'         => $data['code'],
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => 1,
            'creator_id'   => $data['creator_id'],
            'company_id'   => $data['company_id'],
        ])->id;

        $data['lot_stock_location_id'] = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Stock',
            'barcode'      => $data['code'].'STOCK',
            'is_scrap'     => false,
            'is_replenish' => true,
            'parent_id'    => $data['view_location_id'],
            'creator_id'   => $data['creator_id'],
            'company_id'   => $data['company_id'],
        ])->id;

        $data['input_stock_location_id'] = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Input',
            'barcode'      => $data['code'].'INPUT',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $data['view_location_id'],
            'creator_id'   => $data['creator_id'],
            'company_id'   => $data['company_id'],
            'deleted_at'   => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $data['qc_stock_location_id'] = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Quality Control',
            'barcode'      => $data['code'].'QUALITY',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $data['view_location_id'],
            'creator_id'   => $data['creator_id'],
            'company_id'   => $data['company_id'],
            'deleted_at'   => $data['reception_steps'] === ReceptionStep::THREE_STEPS ? null : now(),
        ])->id;

        $data['output_stock_location_id'] = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Output',
            'barcode'      => $data['code'].'OUTPUT',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $data['view_location_id'],
            'creator_id'   => $data['creator_id'],
            'company_id'   => $data['company_id'],
            'deleted_at'   => in_array($data['delivery_steps'], [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $data['pack_stock_location_id'] = Location::create([
            'type'         => LocationType::INTERNAL,
            'name'         => 'Packing Zone',
            'barcode'      => $data['code'].'PACKING',
            'is_scrap'     => false,
            'is_replenish' => false,
            'parent_id'    => $data['view_location_id'],
            'creator_id'   => $data['creator_id'],
            'company_id'   => $data['company_id'],
            'deleted_at'   => $data['delivery_steps'] === DeliveryStep::THREE_STEPS ? null : now(),
        ])->id;

        return $data;
    }

    protected function createOperationTypes(array $data): array
    {
        $supplierLocation = Location::where('type', LocationType::SUPPLIER)->first();

        $customerLocation = Location::where('type', LocationType::CUSTOMER)->first();

        $data['in_type_id'] = OperationType::create([
            'sort'                    => 1,
            'name'                    => 'Receipts',
            'type'                    => Enums\OperationType::INCOMING,
            'sequence_code'           => 'IN',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'IN',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $supplierLocation->id,
            'destination_location_id' => match ($data['reception_steps']) {
                ReceptionStep::ONE_STEP    => $data['lot_stock_location_id'],
                ReceptionStep::TWO_STEPS   => $data['input_stock_location_id'],
                ReceptionStep::THREE_STEPS => $data['input_stock_location_id'],
            },
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
        ])->id;

        $data['out_type_id'] = OperationType::create([
            'sort'                    => 2,
            'name'                    => 'Delivery Orders',
            'type'                    => Enums\OperationType::OUTGOING,
            'sequence_code'           => 'OUT',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'OUT',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => true,
            'show_operations'         => false,
            'source_location_id'      => match ($data['reception_steps']) {
                ReceptionStep::ONE_STEP    => $data['lot_stock_location_id'],
                ReceptionStep::TWO_STEPS   => $data['output_stock_location_id'],
                ReceptionStep::THREE_STEPS => $data['output_stock_location_id'],
            },
            'destination_location_id' => $customerLocation->id,
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
        ])->id;

        $data['pick_type_id'] = OperationType::create([
            'sort'                    => 3,
            'name'                    => 'Pick',
            'type'                    => Enums\OperationType::INTERNAL,
            'sequence_code'           => 'PICK',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'PICK',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $data['lot_stock_location_id'],
            'destination_location_id' => match ($data['delivery_steps']) {
                DeliveryStep::ONE_STEP    => $data['pack_stock_location_id'],
                DeliveryStep::TWO_STEPS   => $data['output_stock_location_id'],
                DeliveryStep::THREE_STEPS => $data['pack_stock_location_id'],
            },
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
            'deleted_at'              => $data['delivery_steps'] === DeliveryStep::ONE_STEP ? now() : null,
        ])->id;

        $data['pack_type_id'] = OperationType::create([
            'sort'                    => 4,
            'name'                    => 'Pack',
            'type'                    => Enums\OperationType::INTERNAL,
            'sequence_code'           => 'PACK',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'PACK',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $data['pack_stock_location_id'],
            'destination_location_id' => $data['output_stock_location_id'],
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
            'deleted_at'              => $data['delivery_steps'] !== DeliveryStep::THREE_STEPS ? now() : null,
        ])->id;

        $data['qc_type_id'] = OperationType::create([
            'sort'                    => 5,
            'name'                    => 'Quality Control',
            'type'                    => Enums\OperationType::INTERNAL,
            'sequence_code'           => 'QC',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'QC',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $data['input_stock_location_id'],
            'destination_location_id' => $data['qc_stock_location_id'],
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
            'deleted_at'              => $data['reception_steps'] !== ReceptionStep::THREE_STEPS ? now() : null,
        ])->id;

        $data['store_type_id'] = OperationType::create([
            'sort'                    => 6,
            'name'                    => 'Storage',
            'type'                    => Enums\OperationType::INTERNAL,
            'sequence_code'           => 'STOR',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'STOR',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => match ($data['reception_steps']) {
                ReceptionStep::ONE_STEP    => $data['input_stock_location_id'],
                ReceptionStep::TWO_STEPS   => $data['input_stock_location_id'],
                ReceptionStep::THREE_STEPS => $data['qc_stock_location_id'],
            },
            'destination_location_id' => $data['lot_stock_location_id'],
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
            'deleted_at'              => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $data['internal_type_id'] = OperationType::create([
            'sort'                    => 7,
            'name'                    => 'Internal Transfers',
            'type'                    => Enums\OperationType::INTERNAL,
            'sequence_code'           => 'INT',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'INT',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $data['lot_stock_location_id'],
            'destination_location_id' => $data['lot_stock_location_id'],
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
            'deleted_at'              => app(WarehouseSettings::class)->enable_locations ? null : now(),
        ])->id;

        $data['xdock_type_id'] = OperationType::create([
            'sort'                    => 8,
            'name'                    => 'Cross Dock',
            'type'                    => Enums\OperationType::INTERNAL,
            'sequence_code'           => 'XD',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'product_label_format'    => '2x7xprice',
            'lot_label_format'        => '4x12_lots',
            'package_label_to_print'  => 'pdf',
            'barcode'                 => $data['code'].'XD',
            'create_backorder'        => CreateBackorder::ASK,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => false,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $data['input_stock_location_id'],
            'destination_location_id' => $data['output_stock_location_id'],
            'company_id'              => $data['company_id'],
            'creator_id'              => $data['creator_id'],
            'deleted_at'              => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($data['delivery_steps'], [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        return $data;
    }

    protected function createRoutes(array $data): array
    {
        $data['reception_route_id'] = Route::create([
            'name' => match ($data['reception_steps']) {
                ReceptionStep::ONE_STEP    => $data['name'].': Receive in 1 step (Stock)',
                ReceptionStep::TWO_STEPS   => $data['name'].': Receive in 2 steps (Input + Stock)',
                ReceptionStep::THREE_STEPS => $data['name'].': Receive in 3 steps (Input + Quality + Stock)',
            },
            'product_selectable'          => false,
            'product_category_selectable' => true,
            'warehouse_selectable'        => true,
            'packaging_selectable'        => false,
            'creator_id'                  => $data['creator_id'],
            'company_id'                  => $data['company_id'],
        ])->id;

        $data['delivery_route_id'] = Route::create([
            'name' => match ($data['delivery_steps']) {
                DeliveryStep::ONE_STEP    => $data['name'].': Deliver in 1 step (Ship)',
                DeliveryStep::TWO_STEPS   => $data['name'].': Deliver in 2 steps (Pick + Ship)',
                DeliveryStep::THREE_STEPS => $data['name'].': Deliver in 3 steps (Pick + Pack + Ship)',
            },
            'product_selectable'          => false,
            'product_category_selectable' => true,
            'warehouse_selectable'        => true,
            'packaging_selectable'        => false,
            'creator_id'                  => $data['creator_id'],
            'company_id'                  => $data['company_id'],
        ])->id;

        $data['crossdock_route_id'] = Route::create([
            'name'                        => $data['name'].': Cross-Dock',
            'product_selectable'          => true,
            'product_category_selectable' => true,
            'warehouse_selectable'        => false,
            'packaging_selectable'        => false,
            'creator_id'                  => $data['creator_id'],
            'company_id'                  => $data['company_id'],
            'deleted_at'                  => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($data['delivery_steps'], [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        return $data;
    }

    protected function createRules(array $data): array
    {
        $supplierLocation = Location::where('type', LocationType::SUPPLIER)->first();

        $customerLocation = Location::where('type', LocationType::CUSTOMER)->first();

        $this->routeIds[] = Rule::create([
            'sort'                     => 1,
            'name'                     => $data['code'].': Vendors → Stock',
            'route_sort'               => 9,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $supplierLocation->id,
            'destination_location_id'  => $data['lot_stock_location_id'],
            'route_id'                 => $data['reception_route_id'],
            'operation_type_id'        => $data['in_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                      => 2,
            'name'                      => $data['code'].': Stock → Customers',
            'route_sort'                => 10,
            'group_propagation_option'  => GroupPropagation::PROPAGATE,
            'action'                    => RuleAction::PULL,
            'procure_method'            => ProcureMethod::MAKE_TO_STOCK,
            'auto'                      => RuleAuto::MANUAL,
            'propagate_cancel'          => false,
            'propagate_carrier'         => true,
            'source_location_id'        => $data['lot_stock_location_id'],
            'destination_location_id'   => $customerLocation->id,
            'route_id'                  => $data['delivery_route_id'],
            'operation_type_id'         => $data['out_type_id'],
            'creator_id'                => $data['creator_id'],
            'company_id'                => $data['company_id'],
            'deleted_at'                => $data['delivery_steps'] === DeliveryStep::ONE_STEP ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 3,
            'name'                     => $data['code'].': Vendors → Customers',
            'route_sort'               => 20,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $supplierLocation->id,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $data['crossdock_route_id'],
            'operation_type_id'        => $data['in_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($data['delivery_steps'], [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 4,
            'name'                     => $data['code'].': Input → Output',
            'route_sort'               => 20,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $data['input_stock_location_id'],
            'destination_location_id'  => $data['output_stock_location_id'],
            'route_id'                 => $data['crossdock_route_id'],
            'operation_type_id'        => $data['xdock_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($data['delivery_steps'], [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        $this->routeIds[] = $data['mto_pull_id'] = Rule::create([
            'sort'                     => 5,
            'name'                     => $data['code'].': Stock → Customers (MTO)',
            'route_sort'               => 5,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $data['lot_stock_location_id'],
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => 1, // To Do: Check how can we not use hardcoded value
            'operation_type_id'        => $data['out_type_id'], // To Do: Need to update based on the condition
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 6,
            'name'                     => $data['code'].': Input → Quality Control',
            'route_sort'               => 6,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => true,
            'propagate_carrier'        => false,
            'source_location_id'       => $data['input_stock_location_id'],
            'destination_location_id'  => $data['qc_stock_location_id'],
            'route_id'                 => $data['reception_route_id'],
            'operation_type_id'        => $data['qc_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => $data['reception_steps'] === ReceptionStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 7,
            'name'                     => $data['code'].': Quality Control → Stock',
            'route_sort'               => 7,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $data['qc_stock_location_id'],
            'destination_location_id'  => $data['lot_stock_location_id'],
            'route_id'                 => $data['reception_route_id'],
            'operation_type_id'        => $data['store_type_id'],
            'operation_type_id'        => $data['qc_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => $data['reception_steps'] === ReceptionStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 8,
            'name'                     => $data['code'].': Stock → Customers',
            'route_sort'               => 8,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $data['lot_stock_location_id'],
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $data['delivery_route_id'],
            'operation_type_id'        => $data['pick_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => $data['delivery_steps'] === DeliveryStep::ONE_STEP ? now() : null,
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 9,
            'name'                     => $data['code'].': Packing Zone → Output',
            'route_sort'               => 9,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $data['pack_stock_location_id'],
            'destination_location_id'  => $data['output_stock_location_id'],
            'route_id'                 => $data['delivery_route_id'],
            'operation_type_id'        => $data['pack_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => $data['delivery_steps'] === DeliveryStep::THREE_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 10,
            'name'                     => $data['code'].': Output → Customers',
            'route_sort'               => 10,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => true,
            'source_location_id'       => $data['output_stock_location_id'],
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $data['delivery_route_id'],
            'operation_type_id'        => $data['out_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => $data['delivery_steps'] === DeliveryStep::ONE_STEP ? now() : null,
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 11,
            'name'                     => $data['code'].': Input → Stock',
            'route_sort'               => 11,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PUSH,
            'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => $data['input_stock_location_id'],
            'destination_location_id'  => $data['lot_stock_location_id'],
            'route_id'                 => $data['reception_route_id'],
            'operation_type_id'        => $data['store_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => $data['delivery_steps'] === ReceptionStep::TWO_STEPS ? null : now(),
        ])->id;

        $this->routeIds[] = Rule::create([
            'sort'                     => 12,
            'name'                     => $data['code'].': False → Customers',
            'route_sort'               => 12,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::BUY,
            'procure_method'           => ProcureMethod::MAKE_TO_STOCK,
            'auto'                     => RuleAuto::MANUAL,
            'propagate_cancel'         => false,
            'propagate_carrier'        => false,
            'source_location_id'       => null,
            'destination_location_id'  => $customerLocation->id,
            'route_id'                 => $data['crossdock_route_id'],
            'operation_type_id'        => $data['in_type_id'],
            'creator_id'               => $data['creator_id'],
            'company_id'               => $data['company_id'],
            'deleted_at'               => in_array($data['reception_steps'], [ReceptionStep::TWO_STEPS, ReceptionStep::THREE_STEPS]) &&
                in_array($data['delivery_steps'], [DeliveryStep::TWO_STEPS, DeliveryStep::THREE_STEPS]) ? null : now(),
        ])->id;

        return $data;
    }
}
