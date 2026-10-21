<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse as BaseWarehouse;

class Warehouse extends BaseWarehouse
{
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'pos_type_id',
            'pos_return_type_id',
        ]);

        parent::__construct($attributes);
    }

    public function posType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'pos_type_id')->withTrashed();
    }

    public function posReturnType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'pos_return_type_id')->withTrashed();
    }

    public function handlePosWarehouseCreation(): void
    {
        if ($this->pos_type_id && $this->pos_return_type_id) {
            return;
        }

        $this->createPosOperationTypes();

        $this->saveQuietly();
    }

    public function finalizePosWarehouseCreation(): void
    {
        $operationTypeIds = array_filter([
            $this->pos_type_id,
            $this->pos_return_type_id,
        ]);

        if (empty($operationTypeIds)) {
            return;
        }

        OperationType::withTrashed()->whereIn('id', $operationTypeIds)->update(['warehouse_id' => $this->id]);

        OperationType::withTrashed()->whereIn('id', $operationTypeIds)->get()->each(
            fn (OperationType $operationType) => $operationType->syncSequence()
        );
    }

    public function syncPosWarehouseConfiguration(): void
    {
        if (! $this->pos_type_id && ! $this->pos_return_type_id) {
            return;
        }

        OperationType::withTrashed()
            ->whereKey($this->pos_type_id)
            ->update([
                'source_location_id'      => $this->lot_stock_location_id,
                'destination_location_id' => $this->posCustomerLocation()->id,
            ]);

        OperationType::withTrashed()
            ->whereKey($this->pos_return_type_id)
            ->update([
                'source_location_id'      => $this->posCustomerLocation()->id,
                'destination_location_id' => $this->lot_stock_location_id,
            ]);
    }

    protected function createPosOperationTypes(): void
    {
        $customerLocation = $this->posCustomerLocation();

        $this->pos_return_type_id ??= OperationType::create([
            'sort'                    => 21,
            'name'                    => 'PoS Returns',
            'type'                    => OperationTypeEnum::INCOMING,
            'sequence_code'           => 'POSRET',
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
            'barcode'                 => $this->code.'POSRET',
            'create_backorder'        => CreateBackorder::NEVER,
            'move_type'               => MoveType::DIRECT,
            'use_create_lots'         => true,
            'use_existing_lots'       => true,
            'print_label'             => false,
            'show_operations'         => false,
            'source_location_id'      => $customerLocation->id,
            'destination_location_id' => $this->lot_stock_location_id,
            'warehouse_id'            => $this->id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
        ])->id;

        $this->pos_type_id ??= OperationType::create([
            'sort'                     => 20,
            'name'                     => 'PoS Orders',
            'type'                     => OperationTypeEnum::OUTGOING,
            'sequence_code'            => 'POS',
            'reservation_method'       => ReservationMethod::AT_CONFIRM,
            'barcode'                  => $this->code.'POS',
            'create_backorder'         => CreateBackorder::NEVER,
            'move_type'                => MoveType::DIRECT,
            'use_create_lots'          => true,
            'use_existing_lots'        => true,
            'print_label'              => false,
            'show_operations'          => false,
            'source_location_id'       => $this->lot_stock_location_id,
            'destination_location_id'  => $customerLocation->id,
            'return_operation_type_id' => $this->pos_return_type_id,
            'warehouse_id'             => $this->id,
            'company_id'               => $this->company_id,
            'creator_id'               => $this->creator_id,
        ])->id;
    }

    protected function posCustomerLocation(): Location
    {
        return Location::withTrashed()
            ->where('type', LocationType::CUSTOMER)
            ->where(owned_by_company($this->company_id))
            ->orderByRaw('company_id IS NOT NULL DESC')
            ->firstOrFail();
    }
}
