<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move as BaseMove;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;

class Move extends BaseMove
{
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'created_order_id',
            'order_id',
            'raw_material_order_id',
            'unbuild_order_id',
            'consume_unbuild_order_id',
            'mo_operation_id',
            'work_order_id',
            'bom_line_id',
            'byproduct_id',
            'order_finished_lot_id',
            'cost_share',
            'manual_consumption',
        ]);

        $this->mergeCasts([
            'cost_share'         => 'decimal:4',
            'manual_consumption' => 'boolean',
        ]);

        parent::__construct($attributes);
    }

    public function createdOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'created_order_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function rawMaterialOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'raw_material_order_id');
    }

    public function unbuildOrder(): BelongsTo
    {
        return $this->belongsTo(UnbuildOrder::class, 'unbuild_order_id');
    }

    public function consumeUnbuildOrder(): BelongsTo
    {
        return $this->belongsTo(UnbuildOrder::class, 'consume_unbuild_order_id');
    }

    public function moOperation(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'mo_operation_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function bomLine(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterialLine::class, 'bom_line_id');
    }

    public function byproduct(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterialByproduct::class, 'byproduct_id');
    }

    public function orderFinishedLot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'order_finished_lot_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function originReturnedMove(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(MoveLine::class, 'move_id');
    }

    public function moveDestinations(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'inventories_move_destinations', 'origin_move_id', 'destination_move_id');
    }

    public function shouldBeAssigned()
    {
        $shouldBeAssigned = parent::shouldBeAssigned();

        return $shouldBeAssigned && ! ($this->order_id or $this->raw_material_order_id);
    }

    public function shouldBypassSetQtyProducing(): bool
    {
        if (in_array($this->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])) {
            return true;
        }

        if (float_is_zero($this->product_uom_qty, precisionRounding: $this->uom->rounding)) {
            return true;
        }

        return false;
    }

    public function getUnitFactorAttribute()
    {
        if ($this->raw_material_order_id || $this->order_id) {
            $order = $this->rawMaterialOrder ?? $this->order;

            return $this->product_uom_qty / (($order->quantity - $order->quantity_produced) ?: 1);
        }

        return 1.0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($move) {
            $mo = $move->rawMaterialOrder ?? $move->order ?? null;

            $locationDestination = Location::find($move->destination_location_id ?? null);

            if (! $mo || ($move->is_scraped ?? false) || ($locationDestination?->is_scrap ?? false)) {
                return;
            }

            $move->name = $mo->name;
            $move->origin = $mo->getOrigin();
            $move->procurement_group_id = $mo->procurement_group_id;
            // $move->propagate_cancel  = $mo->propagate_cancel;

            if (! empty($move->raw_material_order_id)) {
                $move->destination_location_id = $mo->production_location_id;

                $move->scheduled_at = $mo->started_at;

                $move->deadline = $mo->started_at;

                $move->origin = $mo->getOrigin();

                if (empty($move->source_location_id)) {
                    $move->source_location_id = $mo->source_location_id;
                }

                if (in_array($mo->state, [ManufacturingOrderState::PROGRESS, ManufacturingOrderState::TO_CLOSE]) && $mo->quantity_producing > 0) {
                    $move->is_picked = true;
                }

                return;
            }

            $move->source_location_id = $mo->production_location_id;

            $move->scheduled_at = $mo->finished_at;

            $move->deadline = $mo->deadline_at;

            if (empty($move->destination_location_id)) {
                $move->destination_location_id = $mo->destination_location_id;
            }
        });

        static::saving(function ($move) {
            $move->warehouse_id = $move->operationType?->warehouse_id;

            $move->mo_operation_id = $move->bomLine?->operation_id;
        });

        static::created(function ($move) {
            $order = $move->rawMaterialOrder ?? $move->order;

            if (
                ! $order
                || in_array($order->state, [ManufacturingOrderState::DRAFT, ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])
                || in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
            ) {
                return;
            }

            if ($move->raw_material_order_id) {
                $move->adjustProcureMethod();
            }

            InventoryFacade::confirmMoves(collect([$move]));
        });
    }

    public function runProcurement() {}

    public function keyAssignOperation(): array
    {
        $keys = parent::keyAssignOperation();

        $keys[] = $this->created_order_id;

        return $keys;
    }

    public function prepareProcurementValues(): array
    {
        $values = parent::prepareProcurementValues();

        $values['bom_line_id'] = $this->bom_line_id;

        return $values;
    }
}
