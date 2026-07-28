<?php

namespace Webkul\Manufacturing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Manufacturing\Database\Factories\OrderFactory;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialReadyToProduce;
use Webkul\Manufacturing\Enums\ManufacturingOrderPriority;
use Webkul\Manufacturing\Enums\ManufacturingOrderReservationState;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Product\Enums\ProductType;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class Order extends Model
{
    use HasChatter, HasFactory, HasLogActivity, HasPermissionScope;

    protected $table = 'manufacturing_orders';

    protected $fillable = [
        'name',
        'reference',
        'priority',
        'origin',
        'state',
        'reservation_state',
        'consumption',
        'quantity',
        'quantity_producing',
        'product_uom_qty',
        'is_planned',
        'is_locked',
        'deadline_at',
        'started_at',
        'finished_at',
        'production_location_id',
        'procurement_group_id',
        'product_id',
        'uom_id',
        'producing_lot_id',
        'operation_type_id',
        'source_location_id',
        'destination_location_id',
        'final_location_id',
        'bill_of_material_id',
        'assigned_user_id',
        'company_id',
        'order_point_id',
        'creator_id',
    ];

    protected $casts = [
        'priority'           => ManufacturingOrderPriority::class,
        'state'              => ManufacturingOrderState::class,
        'reservation_state'  => ManufacturingOrderReservationState::class,
        'consumption'        => BillOfMaterialConsumption::class,
        'is_planned'         => 'boolean',
        'is_locked'          => 'boolean',
        'quantity'           => 'decimal:4',
        'quantity_producing' => 'decimal:4',
        'product_uom_qty'    => 'decimal:4',
        'deadline_at'        => 'datetime',
        'started_at'         => 'datetime',
        'finished_at'        => 'datetime',
    ];

    protected array $context = [];

    protected function getAssignmentColumn(): ?string
    {
        return 'assigned_user_id';
    }

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function getModelTitle(): string
    {
        return __('manufacturing::models/order.title');
    }

    public function getChatterResponsibles(): array
    {
        return ['assignedUser'];
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'quantity_producing' => __('manufacturing::models/order.log-attributes.product-qty'),
            'state'              => __('manufacturing::models/order.log-attributes.state'),
            'reservation_state'  => __('manufacturing::models/order.log-attributes.reservation-state'),
            'assignedUser.name'  => __('manufacturing::models/order.log-attributes.assigned-user'),
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class)->withTrashed();
    }

    public function producingLot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'producing_lot_id');
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'operation_type_id')->withTrashed();
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'source_location_id')->withTrashed();
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id')->withTrashed();
    }

    public function finalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'final_location_id')->withTrashed();
    }

    public function productionLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'production_location_id')->withTrashed();
    }

    public function billOfMaterial(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_id')->withTrashed();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function orderPoint(): BelongsTo
    {
        return $this->belongsTo(OrderPoint::class, 'order_point_id')->withTrashed();
    }

    public function procurementGroup(): BelongsTo
    {
        return $this->belongsTo(ProcurementGroup::class, 'procurement_group_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'manufacturing_order_id');
    }

    public function rawMaterialMoves(): HasMany
    {
        return $this->hasMany(Move::class, 'raw_material_order_id');
    }

    public function finishedMoves(): HasMany
    {
        return $this->hasMany(Move::class, 'order_id');
    }

    public function moveDestinations(): HasMany
    {
        return $this->hasMany(Move::class, 'created_order_id');
    }

    public function unbuildOrders(): HasMany
    {
        return $this->hasMany(UnbuildOrder::class, 'manufacturing_order_id');
    }

    public function inventoryOperations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Operation::class,
            ProcurementGroup::class,
            'id',
            'procurement_group_id',
            'procurement_group_id',
            'id'
        );
    }

    public function computeInventoryOperations()
    {
        $operations = Operation::where('procurement_group_id', $this->procurement_group_id)
            ->whereNotNull('procurement_group_id')
            ->get();

        $operations = $operations->merge(
            $this->rawMaterialMoves->flatMap->moveOrigins->pluck('operation')->filter()->unique('id')
        );

        return [
            $operations,
            $operations->count(),
        ];
    }

    public function getInventoryOperationsAttribute()
    {
        $operations = Operation::where('procurement_group_id', $this->procurement_group_id)
            ->whereNotNull('procurement_group_id')
            ->get();

        $operations = $operations->merge(
            $this->rawMaterialMoves->flatMap->moveOrigins->pluck('operation')->filter()->unique('id')
        );

        [$operations] = $this->computeInventoryOperations();

        return $operations;
    }

    public function getDeliveryCountAttribute()
    {
        [, $deliveryCount] = $this->computeInventoryOperations();

        return $deliveryCount;
    }

    public function getConsumptionEfficiencyAttribute(): ?float
    {
        $bomLines = $this->billOfMaterial?->lines ?? collect();

        if ($bomLines->isEmpty()) {
            return null;
        }

        $expected = $bomLines->sum(fn ($line) => (float) $line->quantity * (float) $this->quantity);

        $actual = $this->rawMaterialMoves->sum(fn ($move) => (float) $move->quantity);

        if ($actual <= 0) {
            return null;
        }

        return round(($expected / $actual) * 100, 2);
    }

    public function getQuantityProducedAttribute()
    {
        $doneMoves = $this->finishedMoves()
            ->get()
            ->filter(
                fn ($move) => $move->state !== MoveState::CANCELED
                    && $move->product_id === $this->product_id
            );

        return $doneMoves
            ->filter(fn ($move) => $move->is_picked)
            ->sum('quantity');
    }

    public function getMoveByproductsAttribute()
    {
        return $this->finishedMoves
            ->filter(fn ($move) => $move->product_id !== $this->product_id);
    }

    public function getComponentsAvailabilityStateAttribute()
    {
        [$componentAvailabilityState] = $this->getComponentsAvailability();

        return $componentAvailabilityState;
    }

    public function getComponentsAvailabilityAttribute()
    {
        [, $componentAvailability] = $this->getComponentsAvailability();

        return $componentAvailability;
    }

    public function getDocumentIterateKey(Move $move)
    {
        return $move->moveOrigins ? 'moveOrigins' : false;
    }

    public function shouldPostponeDateFinished($dateFinished): bool
    {
        return $dateFinished->equalTo($this->started_at);
    }

    public function getWarehouseAttribute()
    {
        return $this->sourceLocation?->warehouse;
    }

    public function getOrigin()
    {
        $origin = $this->name;

        if ($this->order_point_id && $this->origin) {
            $origin = str_replace(
                sprintf('%s - ', $this->orderPoint->name),
                '',
                $this->origin
            );

            $origin = sprintf('%s,%s', $origin, $this->name);
        }

        return $origin;
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $order): void {
            $authUser = Auth::user();

            $order->creator_id ??= $authUser?->id;

            $order->company_id ??= $authUser?->default_company_id;

            $order->computeState();

            $order->priority ??= ManufacturingOrderPriority::NORMAL;

            $order->consumption ??= BillOfMaterialConsumption::FLEXIBLE;

            $order->started_at ??= now();

            $order->computeSourceLocationId();

            $order->computeDestinationLocationId();

            $order->computeStartedAt();

            $order->computeFinishedAt();

            $order->computeDeadlineAt();

            $order->computeProductionLocationId();
        });

        static::created(function ($order) {
            $name = 'MO/'.$order->id;

            if (! $order->procurement_group_id) {
                $order->procurement_group_id = $order->procurementGroup()->create([
                    'name' => $name,
                ])->id;
            }

            $order->update([
                'name' => $name,
            ]);
        });

        static::saving(function ($order) {
            $order->computeName();

            $order->computeProductUOMQty();

            if ($order->wasChanged('started_at')) {
                $order->computeIsPlanned();
            }

            if ($order->wasChanged(['company_id', 'started_at', 'is_planned', 'product_id'])) {
                $order->computeFinishedAt();
            }

            $order->computeDeadlineAt();
        });

        static::updated(function ($order) {
            if ($order->wasChanged(['product_id', 'bill_of_material_id', 'quantity', 'uom_id', 'destination_location_id', 'finished_at'])) {
                $order->computeFinishedMoves();
            }

            if ($order->wasChanged('state')) {
                $order->computeReservationState();

                $order->saveQuietly();
            }

            if ($order->wasChanged('quantity_producing')) {
                $order->setQuantityProducing();
            }
        });
    }

    public function computeName()
    {
        $this->name = 'MO/'.$this->id;
    }

    public function computeProductUOMQty()
    {
        $this->product_uom_qty = $this->uom->computeQuantity($this->quantity, $this->billOfMaterial->uom ?? $this->product->uom);
    }

    public function computeSourceLocationId()
    {
        $this->source_location_id ??= $this->operationType->source_location_id;
    }

    public function computeDestinationLocationId()
    {
        $this->destination_location_id ??= $this->operationType->destination_location_id;
    }

    public function computeState(): void
    {
        if (! $this->state || ! $this->uom_id || ! $this->id) {
            $this->state = ManufacturingOrderState::DRAFT;

            return;
        }

        if (
            $this->state === ManufacturingOrderState::CANCEL
            || (
                $this->finishedMoves->isNotEmpty()
                && $this->finishedMoves->every(fn ($move) => $move->state === MoveState::CANCELED
                )
            )
        ) {
            $this->state = ManufacturingOrderState::CANCEL;

            return;
        }

        if (
            $this->state === ManufacturingOrderState::DONE ||
            (
                $this->rawMaterialMoves->isNotEmpty() &&
                $this->rawMaterialMoves->every(fn ($m) => in_array($m->state, [MoveState::CANCELED, MoveState::DONE])) &&
                $this->finishedMoves->every(fn ($m) => in_array($m->state, [MoveState::CANCELED, MoveState::DONE]))
            )
        ) {
            $this->state = ManufacturingOrderState::DONE;

            return;
        }

        if (
            $this->workOrders->isNotEmpty()
            && $this->workOrders->every(fn ($wo) => in_array($wo->state, [WorkOrderState::DONE, WorkOrderState::CANCEL]))
        ) {
            $this->state = ManufacturingOrderState::TO_CLOSE;

            return;
        }

        if (
            $this->workOrders->isEmpty()
            && float_compare($this->quantity_producing, $this->quantity, precisionRounding: $this->uom->rounding) >= 0
        ) {
            $this->state = ManufacturingOrderState::TO_CLOSE;

            return;
        }

        if ($this->workOrders->some(fn ($wo) => in_array($wo->state, [WorkOrderState::PROGRESS, WorkOrderState::DONE]))) {
            $this->state = ManufacturingOrderState::PROGRESS;

            return;
        }

        if (
            $this->uom_id &&
            ! float_is_zero($this->quantity_producing, precisionRounding: $this->uom->rounding)
        ) {
            $this->state = ManufacturingOrderState::PROGRESS;

            return;
        }

        if ($this->rawMaterialMoves->some(fn ($move) => $move->is_picked)) {
            $this->state = ManufacturingOrderState::PROGRESS;

            return;
        }
    }

    public function computeReservationState(): void
    {
        if (in_array($this->state, [ManufacturingOrderState::DRAFT, ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])) {
            $this->reservation_state = null;

            return;
        }

        $relevantMoveState = Move::getRelevantStateAmongMoves(
            $this->rawMaterialMoves->filter(fn ($move) => ! $move->is_picked)
        );

        if ($relevantMoveState === MoveState::PARTIALLY_ASSIGNED) {
            if (
                $this->workOrders->pluck('operation_id')->filter()->isNotEmpty()
                && $this->billOfMaterial?->ready_to_produce === BillOfMaterialReadyToProduce::ASAP
            ) {
                $this->reservation_state = $this->getReadyToProduceState();
            } else {
                $this->reservation_state = ManufacturingOrderReservationState::CONFIRMED;
            }
        } elseif ($relevantMoveState !== MoveState::DRAFT) {
            $this->reservation_state = ManufacturingOrderReservationState::from($relevantMoveState->value);
        } else {
            $this->reservation_state = null;
        }
    }

    public function computeStartedAt()
    {
        if ($defaultDateDeadline = ($this->context['default_deadline'] ?? false)) {
            return Carbon::parse($defaultDateDeadline)->subHour();
        }

        return now();
    }

    public function computeDeadlineAt()
    {
        $deadline = $this->finishedMoves
            ->filter(fn ($move) => $move->deadline)
            ->min('deadline');

        $this->deadline_at = $deadline ?? $this->deadline_at;
    }

    public function computeFinishedAt()
    {
        if (! $this->started_at || $this->is_planned || $this->state === ManufacturingOrderState::DONE) {
            return;
        }

        $daysDelay = $this->billOfMaterial?->produce_delay ?? 0;

        $finishedAt = Carbon::parse($this->started_at)->addDays($daysDelay);

        if ($this->shouldPostponeDateFinished($finishedAt)) {
            $workOrderExpectedDuration = $this->workOrders->sum('expected_duration');

            $finishedAt = $finishedAt->addMinutes($workOrderExpectedDuration ?: 60);
        }

        $this->finished_at = $finishedAt;
    }

    public function computeProductionLocationId()
    {
        $this->production_location_id = Location::where('type', 'production')->where('company_id', $this->company_id)->first()?->id;
    }

    public function computeIsPlanned()
    {
        $this->is_planned = $this->workOrders->isNotEmpty()
            && $this->workOrders->some(fn ($workOrder) => $workOrder->started_at && $workOrder->finished_at);
    }

    public function computeFinishedMoves(): void
    {
        if ($this->state !== ManufacturingOrderState::DRAFT) {
            $updatedValues = [];

            if ($this->finished_at) {
                $updatedValues['date'] = $this->finished_at;
            }

            if ($this->deadline_at) {
                $updatedValues['deadline_at'] = $this->deadline_at;
            }

            if (! empty($updatedValues)) {
                $this->finishedMoves->each->update($updatedValues);
            }

            return;
        }

        $this->finishedMoves()->delete();

        if ($this->product_id) {
            $this->updateOrCreateMoveFinished();
        } else {
            $this->finishedMoves()
                ->whereNotNull('bom_line_id')
                ->delete();
        }
    }

    public function getReadyToProduceState()
    {
        $operations = $this->workOrders
            ->pluck('operation')
            ->filter()
            ->values();

        if ($operations->count() === 1) {
            $movesInFirstOperation = $this->rawMaterialMoves;
        } else {
            $firstOperation = $operations->first();

            $movesInFirstOperation = $this->rawMaterialMoves
                ->filter(fn ($move) => $move->mo_operation_id === $firstOperation->id);
        }

        $movesInFirstOperation = $movesInFirstOperation->filter(
            fn ($move) => $move->bom_line_id
                && ! $move->bomLine->skipBomLine(
                    $this->product
                )
        );

        if ($movesInFirstOperation->every(fn ($move) => $move->state === MoveState::ASSIGNED)) {
            return ManufacturingOrderReservationState::ASSIGNED;
        }

        return ManufacturingOrderReservationState::CONFIRMED;
    }

    public function getMoveFinishedValues(
        int $productId,
        float $productUomQty,
        int $productUomId,
        ?int $operationId = null,
        ?int $byproductId = null,
        float $costShare = 0
    ): array {
        $groupOrders = $this->procurementGroup?->orders ?? collect();

        $moveDestinationIds = $this->moveDestinations->pluck('id')->all();

        if ($groupOrders->count() > 1) {
            $additionalDestinationIds = $groupOrders->first()
                ->finishedMoves
                ->filter(fn ($move) => $move->product_id === $this->product_id)
                ->flatMap->moveDestinations
                ->pluck('id')
                ->all();

            $moveDestinationIds = array_unique(array_merge($moveDestinationIds, $additionalDestinationIds));
        }

        return [
            'product_id'              => $productId,
            'product_uom_qty'         => $productUomQty,
            'uom_id'                  => $productUomId,
            'operation_id'            => $operationId,
            'byproduct_id'            => $byproductId,
            'name'                    => 'New',
            'scheduled_at'            => $this->finished_at,
            'deadline'                => $this->deadline_at,
            'operation_type_id'       => $this->operation_type_id,
            'source_location_id'      => $this->production_location_id,
            'destination_location_id' => $this->destination_location_id,
            'company_id'              => $this->company_id,
            'order_id'                => $this->id,
            'warehouse_id'            => $this->destinationLocation->warehouse_id,
            'origin'                  => $this->product->name,
            'procurement_group_id'    => $this->procurementGroup?->id,
            'propagate_cancel'        => $this->propagate_cancel,
            'move_destination_ids'    => ! $byproductId ? $moveDestinationIds : [],
            'cost_share'              => $costShare,
        ];
    }

    public function getMovesFinishedValues(): array
    {
        $moves = [];

        $byproductProductIds = $this->billOfMaterial?->byproducts->pluck('product_id')->all() ?? [];

        if (in_array($this->product_id, $byproductProductIds)) {
            throw new \Exception(__('manufacturing::system.order.product-in-byproducts', [
                'product' => $this->product->name,
            ]));
        }

        $finishedMoveValues = $this->getMoveFinishedValues($this->product_id, $this->quantity, $this->uom_id);

        $finishedMoveValues['final_location_id'] = $this->final_location_id;

        $moves[] = $finishedMoveValues;

        foreach ($this->billOfMaterial?->byproducts ?? [] as $byproduct) {
            if ($byproduct->skipByproductLine($this->product)) {
                continue;
            }

            $productUomFactor = $this->uom->computeQuantity($this->quantity, $this->billOfMaterial->uom ?? $this->product->uom);

            $qty = $byproduct->quantity * ($productUomFactor / ($this->billOfMaterial->quantity ?? 1));

            $moves[] = $this->getMoveFinishedValues(
                $byproduct->product_id,
                $qty,
                $byproduct->uom_id,
                $byproduct->operation_id,
                $byproduct->id,
                $byproduct->cost_share ?? 0
            );
        }

        return $moves;
    }

    public function getMovesRawValues(): array
    {
        $moves = [];

        if (! $this->bill_of_material_id) {
            return $moves;
        }

        $factor = $this->product_uom_qty / $this->billOfMaterial->quantity;

        [, $lines] = $this->billOfMaterial->explode($this->product, $factor);

        foreach ($lines as [$bomLine, $lineData]) {
            if ($bomLine->product->type === ProductType::SERVICE) {
                continue;
            }

            $operationId = $bomLine->operation_id
                ?? ($lineData['parent_line'] ? $lineData['parent_line']->operation_id : null);

            $moves[] = $this->getMoveRawValues(
                $bomLine->product_id,
                $lineData['qty'],
                $bomLine->uom_id,
                $operationId,
                $bomLine->id,
                $bomLine->is_manual_consumption
            );
        }

        return $moves;
    }

    public function getMoveRawValues(
        int $productId,
        float $productUomQty,
        int $uomId,
        ?int $operationId = null,
        ?int $bomLineId = null,
        bool $manualConsumption = false
    ): array {
        return [
            'sort'                    => 10,
            'name'                    => 'New',
            'scheduled_at'            => $this->started_at,
            'deadline'                => $this->started_at,
            'bom_line_id'             => $bomLineId,
            'operation_type_id'       => $this->operation_type_id,
            'product_id'              => $productId,
            'product_uom_qty'         => $productUomQty,
            'uom_id'                  => $uomId,
            'source_location_id'      => $this->source_location_id,
            'destination_location_id' => $this->production_location_id,
            'raw_material_order_id'   => $this->id,
            'company_id'              => $this->company_id,
            'mo_operation_id'         => $operationId,
            'procure_method'          => ProcureMethod::MAKE_TO_STOCK,
            'origin'                  => $this->getOrigin(),
            'warehouse_id'            => $this->sourceLocation->warehouse_id,
            'procurement_group_id'    => $this->procurementGroup?->id,
            'propagate_cancel'        => $this->propagate_cancel,
            'manual_consumption'      => $manualConsumption,
        ];
    }

    public function updateOrCreateMoveFinished(): void
    {
        $movesFinishedValues = $this->getMovesFinishedValues();

        $movesByproductDict = $this->finishedMoves()->get()
            ->filter(fn ($move) => $move->byproduct_id)
            ->keyBy('byproduct_id');

        $moveFinished = $this->finishedMoves()->get()
            ->filter(fn ($move) => $move->product_id === $this->product_id)
            ->first();

        foreach ($movesFinishedValues as $moveFinishedValues) {
            if (isset($moveFinishedValues['byproduct_id']) && $movesByproductDict->has($moveFinishedValues['byproduct_id'])) {
                $movesByproductDict->get($moveFinishedValues['byproduct_id'])->update($moveFinishedValues);
            } elseif (isset($moveFinishedValues['product_id']) && $moveFinishedValues['product_id'] === $this->product_id && $moveFinished) {
                $moveFinished->update($moveFinishedValues);
            } else {
                $this->finishedMoves()->create($moveFinishedValues);
            }
        }
    }

    public function linkWorkOrdersAndMoves(): void
    {
        $workOrderPerOperation = $this->workOrders
            ->filter(fn ($workOrder) => $workOrder->operation_id)
            ->keyBy('operation_id');

        $workOrderBillOfMaterials = $this->workOrders
            ->pluck('operation.bill_of_material_id')
            ->unique()
            ->filter();

        $lastWorkOrderPerBom = [];

        $allowWorkOrderDependencies = $this->billOfMaterial?->allow_operation_dependencies;

        $workOrderOrder = fn ($workOrder) => [$workOrder->sort, $workOrder->id];

        if ($allowWorkOrderDependencies) {
            foreach ($this->workOrders->sortBy($workOrderOrder) as $workOrder) {
                if ($workOrder->operation) {
                    $blockedByIds = $workOrder->operation->blockedByOperations
                        ->filter(fn ($operationId) => $workOrderPerOperation->has($operationId))
                        ->map(fn ($operationId) => $workOrderPerOperation->get($operationId)->id)
                        ->all();

                    $workOrder->blockedByWorkOrders()->syncWithoutDetaching($blockedByIds);
                }

                if ($workOrder->dependentWorkOrders->isEmpty()) {
                    $bomId = $workOrder->operation?->bill_of_material_id;

                    $lastWorkOrderPerBom[$bomId] = $workOrder;
                }
            }
        } else {
            $previousWorkOrder = null;

            foreach ($this->workOrders->sortBy($workOrderOrder) as $workOrder) {
                if ($previousWorkOrder) {
                    $workOrder->blockedByWorkOrders()->syncWithoutDetaching([$previousWorkOrder->id]);

                    $previousWorkOrder->computeState();

                    $previousWorkOrder->save();
                }

                $previousWorkOrder = $workOrder;

                $bomId = $workOrder->operation?->bill_of_material_id;

                $lastWorkOrderPerBom[$bomId] = $workOrder;
            }
        }

        $allMoves = $this->rawMaterialMoves->merge($this->finishedMoves);

        foreach ($allMoves as $move) {
            if ($move->mo_operation_id) {
                $move->update([
                    'work_order_id' => $workOrderPerOperation->has($move->mo_operation_id)
                        ? $workOrderPerOperation->get($move->mo_operation_id)->id
                        : null,
                ]);
            } else {
                $bom = ($move->bomLine && $workOrderBillOfMaterials->contains($move->bomLine->bill_of_material_id))
                    ? $move->bomLine->bill_of_material_id
                    : $this->bill_of_material_id;

                $move->update([
                    'work_order_id' => isset($lastWorkOrderPerBom[$bom]) ? $lastWorkOrderPerBom[$bom]->id : null,
                ]);
            }
        }
    }

    public function setQuantities(): void
    {
        $missingLotIdProducts = '';

        if (in_array($this->product->tracking, [ProductTracking::LOT, ProductTracking::SERIAL]) && ! $this->producing_lot_id) {
            $this->generateLot();
        }

        if (
            $this->product->tracking === ProductTracking::SERIAL
            && float_compare($this->quantity_producing, 1, precisionRounding: $this->uom->rounding) === 1
        ) {
            $this->update(['quantity_producing' => 1]);
        } else {
            $this->update(['quantity_producing' => $this->quantity - $this->quantity_produced]);
        }

        $this->setQuantityProducing();

        foreach ($this->rawMaterialMoves as $move) {
            if (
                in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
                || ! $move->product_uom_qty
            ) {
                continue;
            }

            if (
                $move->manual_consumption
                && in_array($move->product->tracking, [ProductTracking::LOT, ProductTracking::SERIAL])
                && (
                    ! $move->is_picked
                    || $move->lines->filter(fn ($line) => $line->quantity && $line->is_picked && ! $line->lot_id)->isNotEmpty()
                )
            ) {
                $missingLotIdProducts .= "\n  - {$move->product->name}";
            }
        }

        if ($missingLotIdProducts) {
            throw new \Exception(__('manufacturing::system.order.missing-lot-serial-number', [
                'missing_products' => $missingLotIdProducts,
            ]));
        }
    }

    public function setQuantityProducing(bool $pickManualConsumptionMoves = true): void
    {
        if ($this->product->tracking === ProductTracking::SERIAL) {
            $qtyProducingUom = $this->product->uom->computeQuantity($this->quantity_producing, $this->product->uom, roundingMethod: 'HALF_UP');

            if (
                $qtyProducingUom != 1
                && ! (
                    $qtyProducingUom == 0
                    && $this->getOriginal('quantity_producing') != $this->quantity_producing
                )
            ) {
                $this->update([
                    'quantity_producing' => $this->product->uom->computeQuantity(1, $this->uom, roundingMethod: 'HALF_UP'),
                ]);
            }
        }

        $isWaiting = $this->warehouse->manufacture_steps !== ManufactureStep::ONE_STEP
            && $this->inventoryOperations->some(
                fn ($operation) => $operation->operation_type_id === $this->warehouse->pbm_type_id
                    && ! in_array($operation->state, [OperationState::DONE, OperationState::CANCELED])
            );

        $rawMoves = $this->rawMaterialMoves()->get()->filter(fn ($move) => ! $isWaiting || $move->product->tracking === ProductTracking::QTY);

        $finishedMoves = $this->finishedMoves()->get()->filter(fn ($move) => $move->product_id !== $this->product_id);

        foreach ($rawMoves->merge($finishedMoves) as $move) {
            if ($move->manual_consumption && $move->is_picked) {
                continue;
            }

            if ($move->shouldBypassSetQtyProducing()) {
                continue;
            }

            $newQty = float_round(
                ($this->quantity_producing - $this->quantity_produced) * $move->unit_factor,
                precisionRounding: $move->uom->rounding
            );

            $move->setQuantityDone($newQty);

            if (! $move->manual_consumption || $pickManualConsumptionMoves) {
                $move->update(['is_picked' => true]);
            }
        }
    }

    public function autoProductionChecks(): bool
    {
        $products = $this->rawMaterialMoves->pluck('product')
            ->merge($this->finishedMoves->pluck('product'))
            ->unique('id');

        return $products->every(fn ($product) => $product->tracking === ProductTracking::QTY)
            || $this->product_uom_qty == 1
            || (
                $this->product->tracking !== ProductTracking::SERIAL
                && in_array($this->reservation_state, [
                    ManufacturingOrderReservationState::ASSIGNED,
                    ManufacturingOrderReservationState::CONFIRMED,
                    ManufacturingOrderReservationState::WAITING,
                ])
            );
    }

    public function checkSnUniqueness(): void
    {
        if (
            $this->product->tracking === ProductTracking::SERIAL
            && $this->producing_lot_id
        ) {
            if ($this->isFinishedSnAlreadyProduced($this->producingLot)) {
                throw new \Exception(__('manufacturing::system.order.serial-number-already-produced', [
                    'product' => $this->product->name,
                ]));
            }
        }

        foreach ($this->finishedMoves as $move) {
            if (
                $move->product->tracking !== ProductTracking::SERIAL
                || $move->product_id === $this->product_id
            ) {
                continue;
            }

            foreach ($move->lines as $moveLine) {
                if (float_is_zero($moveLine->quantity, precisionRounding: $moveLine->uom->rounding)) {
                    continue;
                }

                if ($this->isFinishedSnAlreadyProduced($moveLine->lot, excludedSml: $moveLine)) {
                    throw new \Exception(__('manufacturing::system.order.byproduct-serial-number-already-produced', [
                        'number'  => $moveLine->lot->name,
                        'product' => $moveLine->product->name,
                    ]));
                }
            }
        }

        $consumedSnIds = [];

        $snErrorMsg = [];

        foreach ($this->rawMaterialMoves as $move) {
            if (
                $move->product->tracking !== ProductTracking::SERIAL
                || ! $move->is_picked
            ) {
                continue;
            }

            foreach ($move->lines as $moveLine) {
                if (
                    ! $moveLine->is_picked
                    || float_is_zero($moveLine->quantity, precisionRounding: $moveLine->uom->rounding)
                    || ! $moveLine->lot_id
                ) {
                    continue;
                }

                $smlSn = $moveLine->lot;

                $message = __('manufacturing::system.order.component-serial-number-consumed', [
                    'number'    => $smlSn->name,
                    'component' => $moveLine->product->name,
                ]);

                $consumedSnIds[] = $smlSn->id;

                $snErrorMsg[$smlSn->id] = $message;

                $coProdMoveLines = $this->rawMaterialMoves->flatMap->lines;

                $duplicates = $coProdMoveLines
                    ->filter(fn ($moveLine) => $moveLine->quantity && $moveLine->lot_id === $smlSn->id)
                    ->filter(fn ($moveLine) => $moveLine->id !== $moveLine->id);

                if ($duplicates->isNotEmpty()) {
                    throw new \Exception($message);
                }
            }
        }

        if (empty($consumedSnIds)) {
            return;
        }

        $consumedSmlGroups = MoveLine::whereIn('lot_id', $consumedSnIds)
            ->where('quantity', 1)
            ->where('state', MoveState::DONE)
            ->whereHas('destinationLocation', fn ($q) => $q->where('type', LocationType::PRODUCTION))
            ->whereNotNull('order_id')
            ->groupBy('lot_id')
            ->selectRaw('lot_id, SUM(quantity) as total')
            ->pluck('total', 'lot_id')
            ->all();

        $problematicSnIds = array_keys($consumedSmlGroups);

        if (empty($problematicSnIds)) {
            return;
        }

        $cancelledSmlGroups = MoveLine::whereIn('lot_id', $problematicSnIds)
            ->where('quantity', 1)
            ->where('state', MoveState::DONE)
            ->whereHas('sourceLocation', fn ($q) => $q->where('type', LocationType::PRODUCTION))
            ->whereDoesntHave('move.order')
            ->groupBy('lot_id')
            ->selectRaw('lot_id, SUM(quantity) as total')
            ->pluck('total', 'lot_id')
            ->all();

        foreach ($problematicSnIds as $snId) {
            $consumedQty = $consumedSmlGroups[$snId];

            $cancelledQty = $cancelledSmlGroups[$snId] ?? 0.0;

            if ($consumedQty - $cancelledQty > 0) {
                throw new \Exception($snErrorMsg[$snId]);
            }
        }
    }

    public function generateLot()
    {
        $name = Lot::getNextSerial($this->company, $this->product);

        $this->update([
            'producing_lot_id' => Lot::create([
                'product_id' => $this->product_id,
                'name'       => $name,
            ])->id,
        ]);

        if ($this->product->tracking === ProductTracking::SERIAL) {
            $this->setQuantityProducing();
        }
    }

    public function getConsumptionIssues(): array
    {
        if ($this->context['skip_consumption'] ?? false) {
            return [];
        }

        $issues = [];

        if (
            $this->consumption === BillOfMaterialConsumption::FLEXIBLE
            || ! $this->bill_of_material_id
            || $this->billOfMaterial?->lines->isEmpty()
        ) {
            return $issues;
        }

        $expectedMoveValues = $this->getMovesRawValues();

        $expectedQtyByProduct = [];

        foreach ($expectedMoveValues as $moveValues) {
            $moveProduct = Product::find($moveValues['product_id']);

            $moveUom = UOM::find($moveValues['uom_id']);

            $moveProductQty = $moveUom->computeQuantity($moveValues['product_uom_qty'], $moveProduct->uom);

            $productId = $moveProduct->id;

            $expectedQtyByProduct[$productId] = ($expectedQtyByProduct[$productId] ?? 0.0)
                + $moveProductQty * $this->quantity_producing / $this->quantity;
        }

        $doneQtyByProduct = [];

        foreach ($this->rawMaterialMoves as $move) {
            $quantity = $move->uom->computeQuantity($move->getPickedQuantity(), $move->product->uom);

            $rounding = $move->product->uom->rounding;

            $productId = $move->product_id;

            if (
                ! isset($expectedQtyByProduct[$productId])
                && $move->is_picked
                && ! float_is_zero($quantity, precisionRounding: $rounding)
            ) {
                $issues[] = [$this, $move->product, $quantity, 0.0];

                continue;
            }

            $doneQtyByProduct[$productId] = ($doneQtyByProduct[$productId] ?? 0.0)
                + ($move->is_picked ? $quantity : 0.0);
        }

        foreach ($expectedQtyByProduct as $productId => $qtyToConsume) {
            $product = Product::find($productId);

            $quantity = $doneQtyByProduct[$productId] ?? 0.0;

            if (float_compare($qtyToConsume, $quantity, precisionRounding: $product->uom->rounding) !== 0) {
                $issues[] = [$this, $product, $quantity, $qtyToConsume];
            }
        }

        return $issues;
    }

    public function getQuantityProducedIssues(): array
    {
        $quantityIssues = [];

        if ($this->context['skip_back_order'] ?? false) {
            return $quantityIssues;
        }

        if (! float_is_zero($this->getQuantityToBackOrder(), precisionRounding: $this->uom->rounding)) {
            $quantityIssues[] = $this;
        }

        return $quantityIssues;
    }

    public function getQuantityToBackOrder(): float
    {
        return max($this->quantity - $this->quantity_producing, 0);
    }

    public function isFinishedSnAlreadyProduced(Lot $lot, ?MoveLine $excludedSml = null): bool
    {
        if (! $lot) {
            return false;
        }

        $coProdMoveLines = $this->finishedMoves->flatMap->lines;

        if ($excludedSml) {
            $coProdMoveLines = $coProdMoveLines->filter(fn ($moveLine) => $moveLine->id !== $excludedSml->id);
        }

        $duplicates = MoveLine::where('lot_id', $lot->id)
            ->where('quantity', 1)
            ->where('state', MoveState::DONE)
            ->whereHas('sourceLocation', fn ($q) => $q->where('type', LocationType::PRODUCTION))
            ->whereHas('move', fn ($q) => $q->whereNull('unbuild_order_id'))
            ->count();

        if ($duplicates) {
            $duplicatesUnbuild = MoveLine::where('lot_id', $lot->id)
                ->where('quantity', 1)
                ->where('state', MoveState::DONE)
                ->whereNull('order_id')
                ->whereHas('destinationLocation', fn ($q) => $q->where('type', LocationType::PRODUCTION))
                ->whereHas('move', fn ($q) => $q->whereNotNull('unbuild_order_id'))
                ->count();

            $removed = MoveLine::where('lot_id', $lot->id)
                ->where('state', MoveState::DONE)
                ->whereHas('sourceLocation', fn ($q) => $q->where('is_scrap', false))
                ->whereHas('destinationLocation', fn ($q) => $q->where('is_scrap', true))
                ->count();

            $unremoved = MoveLine::where('lot_id', $lot->id)
                ->where('state', MoveState::DONE)
                ->whereHas('sourceLocation', fn ($q) => $q->where('is_scrap', true))
                ->whereHas('destinationLocation', fn ($q) => $q->where('is_scrap', false))
                ->count();

            if (! (($duplicatesUnbuild || $removed) && $duplicates - $duplicatesUnbuild - $removed + $unremoved === 0)) {
                return true;
            }
        }

        $duplicates = $coProdMoveLines->filter(fn ($moveLine) => $moveLine->quantity && $moveLine->lot_id === $lot->id);

        return $duplicates->isNotEmpty();
    }

    public function getComponentsAvailability(): array
    {
        $componentsAvailabilityState = null;

        $componentsAvailability = null;

        if (in_array($this->state, [ManufacturingOrderState::CANCEL, ManufacturingOrderState::DONE, ManufacturingOrderState::DRAFT])) {
            return [
                $componentsAvailabilityState,
                $componentsAvailability,
            ];
        }

        $componentsAvailabilityState = 'available';

        $componentsAvailability = __('manufacturing::system.order.components-availability.available');

        $hasUnavailable = $this->rawMaterialMoves->some(function ($move) {
            $threshold = $move->state === MoveState::DRAFT ? 0 : $move->product_qty;

            return float_compare($move->forecast_availability, $threshold, precisionRounding: $move->product->uom->rounding) === -1;
        });

        if ($hasUnavailable) {
            $componentsAvailabilityState = 'unavailable';

            $componentsAvailability = __('manufacturing::system.order.components-availability.not-available');

            return [
                $componentsAvailabilityState,
                $componentsAvailability,
            ];
        }

        $forecastDate = $this->rawMaterialMoves
            ->filter(fn ($move) => $move->forecast_expected_date)
            ->max(fn ($move) => $move->forecast_expected_date);

        if ($forecastDate) {
            if ($this->started_at) {
                $componentsAvailabilityState = Carbon::parse($forecastDate)->gt(Carbon::parse($this->started_at))
                    ? 'late'
                    : 'expected';
            }

            $componentsAvailability = __('manufacturing::system.order.components-availability.expected', [
                'date' => Carbon::parse($forecastDate)->format('Y-m-d'),
            ]);
        }

        return [
            $componentsAvailabilityState,
            $componentsAvailability,
        ];
    }
}
