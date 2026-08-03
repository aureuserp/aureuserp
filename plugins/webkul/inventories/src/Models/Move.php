<?php

namespace Webkul\Inventory\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\MoveFactory;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Support\ProcurementOptions;
use Webkul\Inventory\Support\StockScope;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Models\OrderLine as PurchaseOrderLine;
use Webkul\Sale\Models\OrderLine as SaleOrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;
use Webkul\Support\Traits\BelongsToCompany;

class Move extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'inventories_moves';

    protected $fillable = [
        'name',
        'state',
        'origin',
        'procure_method',
        'reference',
        'description_picking',
        'next_serial',
        'next_serial_count',
        'is_favorite',
        'product_qty',
        'product_uom_qty',
        'quantity',
        'is_picked',
        'is_scraped',
        'is_inventory',
        'additional',
        'is_refund',
        'deadline',
        'reservation_date',
        'scheduled_at',
        'product_id',
        'uom_id',
        'source_location_id',
        'destination_location_id',
        'final_location_id',
        'partner_id',
        'operation_id',
        'rule_id',
        'operation_type_id',
        'origin_returned_move_id',
        'restrict_partner_id',
        'warehouse_id',
        'product_packaging_id',
        'scrap_id',
        'price_unit',
        'company_id',
        'creator_id',
        'procurement_group_id',
        'purchase_order_line_id',
        'sale_order_line_id',
        'bom_line_id',
    ];

    protected $casts = [
        'state'            => MoveState::class,
        'procure_method'   => ProcureMethod::class,
        'quantity'         => 'float',
        'product_qty'      => 'float',
        'product_uom_qty'  => 'float',
        'is_favorite'      => 'boolean',
        'is_picked'        => 'boolean',
        'is_scraped'       => 'boolean',
        'is_inventory'     => 'boolean',
        'additional'       => 'boolean',
        'is_refund'        => 'boolean',
        'reservation_date' => 'date',
        'scheduled_at'     => 'datetime',
        'deadline'         => 'datetime',
        'alert_Date'       => 'datetime',
    ];

    protected static bool $flagNextAsAdditional = false;

    protected bool $isAdditionalCandidate = false;

    protected bool $skipUnreserveOnQuantityChange = false;

    public static function markNextAsAdditional(): void
    {
        static::$flagNextAsAdditional = true;
    }

    public static function forgetAdditionalFlag(): void
    {
        static::$flagNextAsAdditional = false;
    }

    public static function flagsNextAsAdditional(): bool
    {
        return static::$flagNextAsAdditional;
    }

    public function isPurchaseReturn()
    {
        return $this->destinationLocation->type === LocationType::SUPPLIER
            || (
                $this->originReturnedMove
                && $this->destinationLocation->id === $this->destinationLocation->company->inter_company_location_id
            );
    }

    public function isDropshipped()
    {
        return (
            $this->sourceLocation->type === LocationType::SUPPLIER
            || ($this->sourceLocation->type === LocationType::TRANSIT && ! $this->sourceLocation->company_id)
        )
            && (
                $this->destinationLocation->type === LocationType::CUSTOMER
                || ($this->destinationLocation->type === LocationType::TRANSIT && ! $this->destinationLocation->company_id)
            );
    }

    public function isDropshippedReturned()
    {
        return (
            $this->sourceLocation->type === LocationType::CUSTOMER
            || ($this->sourceLocation->type === LocationType::TRANSIT && ! $this->sourceLocation->company_id)
        )
            && (
                $this->destinationLocation->type === LocationType::SUPPLIER
                || ($this->destinationLocation->type === LocationType::TRANSIT && ! $this->destinationLocation->company_id)
            );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function finalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function scrap(): BelongsTo
    {
        return $this->belongsTo(Scrap::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class);
    }

    public function originReturnedMove(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function returnedMoves(): HasMany
    {
        return $this->hasMany(self::class, 'origin_returned_move_id');
    }

    public function restrictPartner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function packageLevel(): BelongsTo
    {
        return $this->belongsTo(PackageLevel::class);
    }

    public function productPackaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(MoveLine::class)->orderBy('id');
    }

    public function moveOrigins(): BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'inventories_move_destinations', 'destination_move_id', 'origin_move_id');
    }

    public function moveDestinations(): BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'inventories_move_destinations', 'origin_move_id', 'destination_move_id');
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_route_moves', 'move_id', 'route_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bypassesReservation(?Location $location = null): bool
    {
        return ($location ?? $this->sourceLocation)->bypassesReservation() || ! $this->product->is_storable;
    }

    public function skipsPushRules(): bool
    {
        return $this->is_inventory
            || $this->moveDestinations->some(fn (Move $move) => $move->sourceLocation->isDescendantOf($this->destinationLocation))
            || ($this->final_location_id && $this->finalLocation->isDescendantOf($this->destinationLocation));
    }

    public function procurementGroup(): BelongsTo
    {
        return $this->belongsTo(ProcurementGroup::class, 'procurement_group_id');
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class, 'purchase_order_line_id');
    }

    public function purchaseOrderLines(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrderLine::class, 'purchases_order_line_moves', 'inventory_move_id', 'purchase_order_line_id');
    }

    public function saleOrderLine(): BelongsTo
    {
        return $this->belongsTo(SaleOrderLine::class, 'sale_order_line_id');
    }

    public function needsOperation()
    {
        return ! $this->operation_id and $this->operation_type_id;
    }

    public function getForecastAvailabilityAttribute()
    {
        [$availability] = $this->forecast();

        return $availability;
    }

    public function getForecastExpectedDateAttribute()
    {
        [, $expectedDate] = $this->forecast();

        return $expectedDate;
    }

    protected static function newFactory(): MoveFactory
    {
        return MoveFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Move $move) {
            $move->creator_id ??= Auth::id();

            $move->company_id ??= $move->operation?->company_id ?? $move->operationType?->company_id;

            $move->state ??= MoveState::DRAFT;

            $move->isAdditionalCandidate = static::$flagNextAsAdditional;

            static::$flagNextAsAdditional = false;

            if ($move->operation && $move->isAdditionalCandidate) {
                $move->markAsAdditionalTo($move->operation);
            }
        });

        static::created(function (Move $move) {
            if (! $move->isAdditionalCandidate || ! $move->additional) {
                return;
            }

            $move->operation->confirmAdditionalMoves();
        });

        static::saving(function (Move $move) {
            $move->applyDefaults();
        });

        static::updated(function (Move $move) {
            if ($move->wasChanged('quantity')) {
                $move->syncLinesToQuantity();
            }

            $movesToReassign = collect();

            if ($move->wasChanged('product_uom_qty') && ! $move->skipUnreserveOnQuantityChange) {
                $movesToReassign = $movesToReassign->merge($move->handleDemandChange());
            }

            if ($move->wasChanged('state')) {
                $move->lines->each(fn (MoveLine $line) => $line->update(['state' => $line->move->refresh()->state]));

                if ($operation = $move->operation) {
                    $operation->refresh();

                    $operation->computeState();

                    $operation->save();
                }
            }

            if ($move->wasChanged('is_picked')) {
                $move->lines()->get()->each(fn (MoveLine $line) => $line->update(['is_picked' => $move->is_picked]));
            }

            if ($move->wasChanged('source_location_id')) {
                $move->dropLinesOutsideSourceLocation();

                $movesToReassign->push($move->refresh());
            }

            if ($move->wasChanged('source_location_id') || $move->wasChanged('destination_location_id')) {
                $move->realignWarehouse();
            }

            if ($movesToReassign->isNotEmpty()) {
                InventoryFacade::reserveMoves($movesToReassign->unique('id'));
            }
        });

        static::deleting(function (Move $move) {
            $move->lines->each->delete();
        });
    }

    protected function markAsAdditionalTo(Operation $operation): void
    {
        if ($operation->state === OperationState::DONE) {
            $this->state = MoveState::DONE;

            $this->additional = true;

            return;
        }

        if (! in_array($operation->state, [OperationState::DRAFT, OperationState::DONE, OperationState::CANCELED])) {
            $this->additional = true;
        }
    }

    protected function handleDemandChange(): Collection
    {
        $overReserved = ! in_array($this->state, [MoveState::DRAFT, MoveState::DONE, MoveState::CANCELED])
            && float_compare($this->quantity, $this->product_uom_qty ?? null, precisionRounding: $this->uom->rounding) === 1;

        $fromSupplier = $this->sourceLocation->type === LocationType::SUPPLIER;

        if ($overReserved) {
            InventoryFacade::releaseMoves(collect([$this]));

            return $fromSupplier ? collect([$this->refresh()]) : collect();
        }

        if ($this->state === MoveState::ASSIGNED) {
            $this->update(['state' => MoveState::PARTIALLY_ASSIGNED]);
        }

        return $fromSupplier && in_array($this->state, [MoveState::PARTIALLY_ASSIGNED, MoveState::ASSIGNED])
            ? collect([$this])
            : collect();
    }

    protected function dropLinesOutsideSourceLocation(): void
    {
        $this->load('sourceLocation');

        foreach ($this->lines()->get() as $line) {
            if ($line->sourceLocation->isDescendantOf($this->sourceLocation)) {
                continue;
            }

            $this->procure_method = ProcureMethod::MAKE_TO_STOCK;

            $this->saveQuietly();

            $this->moveOrigins()->detach();

            $line->delete();
        }
    }

    protected function realignWarehouse(): void
    {
        $this->load('sourceLocation', 'destinationLocation');

        $warehouseId = $this->sourceLocation?->warehouse_id ?? $this->destinationLocation?->warehouse_id;

        if ($warehouseId !== $this->warehouse_id) {
            $this->warehouse_id = $warehouseId;

            $this->saveQuietly();
        }
    }

    protected function applyDefaults(): void
    {
        $this->warehouse_id ??= $this->operation?->destinationLocation->warehouse_id;

        $this->name ??= $this->product->name;

        $this->reference ??= $this->operation?->name;

        $this->uom_id ??= $this->product?->uom_id;

        $this->applyQuantityDefaults();

        $this->procure_method ??= ProcureMethod::MAKE_TO_STOCK;

        $this->partner_id ??= $this->operation?->partner_id;

        $this->operation_type_id ??= $this->operation?->operation_type_id;

        $this->source_location_id ??= $this->operation?->source_location_id ?? $this->operationType?->source_location_id;

        $this->destination_location_id ??= $this->operation?->destination_location_id ?? $this->operationType?->destination_location_id;

        $this->scheduled_at ??= $this->operation?->scheduled_at ?? now();
    }

    protected function applyQuantityDefaults(): void
    {
        $stale = $this->product_qty === null || $this->isDirty(['product_uom_qty', 'uom_id', 'product_id']);

        if ($stale && $this->product_uom_qty !== null) {
            $this->product_qty = $this->uom?->computeQuantity($this->product_uom_qty, $this->product->uom, roundingMethod: 'HALF-UP');
        }

        $this->product_uom_qty ??= $this->product->uom?->computeQuantity($this->product_qty, $this->uom, roundingMethod: 'HALF-UP');
    }

    public function procurementOrigin()
    {
        return $this->procurementGroup?->name ?? ($this->origin ?: $this->operation?->name ?: '/');
    }

    public function pickedQuantity()
    {
        return $this->lines
            ->where('is_picked', true)
            ->sum(fn (MoveLine $line) => $line->uom->computeQuantity($line->qty, $this->uom, roundingMethod: 'HALF-UP'));
    }

    public function resolveProcureMethod($operationTypeCode = false)
    {
        $filters = [
            'source_location_id'      => $this->source_location_id,
            'destination_location_id' => $this->destination_location_id,
            'action'                  => ['!=', RuleAction::PUSH],
        ];

        if ($operationTypeCode) {
            $filters['operationType.type'] = $operationTypeCode;
        }

        $rule = InventoryFacade::matchRule(collect(), $this->productPackaging, $this->product, $this->warehouse, $filters);

        if (! $rule) {
            $this->procure_method = ProcureMethod::MAKE_TO_STOCK;

            return;
        }

        $this->rule_id = $rule->id;

        $this->procure_method = in_array($rule->procure_method, [ProcureMethod::MAKE_TO_STOCK, ProcureMethod::MAKE_TO_ORDER])
            ? $rule->procure_method
            : ProcureMethod::MAKE_TO_STOCK;
    }

    public function syncLinesToQuantity(): void
    {
        $roundedToUom = float_round($this->quantity, precisionRounding: $this->uom->rounding, roundingMethod: 'HALF-UP');

        $roundedToDigits = float_round($this->quantity, precisionDigits: 2, roundingMethod: 'HALF-UP');

        if (float_compare($roundedToUom, $roundedToDigits, precisionDigits: 2) !== 0) {
            throw new \Exception(__('The quantity done for the product :product doesn\'t respect the rounding precision defined on the unit of measure :unit. Please change the quantity done or the rounding precision of your unit of measure.', [
                'product' => $this->product->name,
                'unit'    => $this->uom->name,
            ]));
        }

        $delta = $this->quantity - $this->lineQuantityTotal();

        if (float_compare($delta, 0, precisionRounding: $this->uom->rounding) > 0) {
            $this->distributeQuantityAcrossLines($this->quantity);
        } elseif (float_compare($delta, 0, precisionRounding: $this->uom->rounding) < 0) {
            $this->reduceLinesBy(abs($delta));
        }
    }

    protected function reduceLinesBy(float $quantity): void
    {
        $emptied = collect();

        foreach ($this->lines->sortByDesc('id') as $line) {
            if (float_is_zero($quantity, precisionRounding: $this->uom->rounding)) {
                break;
            }

            $reduction = min($line->qty, $line->uom->computeQuantity($quantity, $line->uom, round: false));

            if (float_is_zero($reduction, precisionRounding: $line->uom->rounding)) {
                continue;
            }

            $clearsLine = float_compare($line->qty, $reduction, precisionRounding: $line->uom->rounding) === 0
                && ! in_array($line->state, [MoveState::DONE, MoveState::CANCELED]);

            if ($clearsLine) {
                $emptied->push($line->id);
            } else {
                $line->update(['qty' => $line->qty - $reduction]);
            }

            $quantity -= $this->uom->computeQuantity($reduction, $this->uom, round: false);
        }

        MoveLine::whereIn('id', $emptied)->get()->each->delete();
    }

    public function distributeQuantityAcrossLines(float $quantity): Collection
    {
        $emptied = collect();

        $adjustments = [];

        foreach ($this->lines as $line) {
            if (float_is_zero($quantity, precisionRounding: $this->uom->rounding)) {
                $emptied->push($line->id);

                continue;
            }

            if (float_compare($line->qty, 0, precisionRounding: $line->uom->rounding) <= 0) {
                continue;
            }

            $sameUom = $line->uom->id === $this->uom->id;

            $lineQty = $sameUom ? $line->qty : $line->uom->computeQuantity($line->qty, $this->uom, round: false);

            $taken = min($quantity, $lineQty);

            if (! $sameUom) {
                $taken = $this->uom->computeQuantity($taken, $line->uom, round: false);
            }

            $taken = float_round($taken, precisionRounding: $line->uom->rounding);

            $adjustments[$line->id] = $taken;

            $quantity -= $sameUom ? $taken : $line->uom->computeQuantity($taken, $this->uom, round: false);
        }

        $newLines = $this->buildOverflowLines($quantity);

        MoveLine::whereIn('id', $emptied)->get()->each->delete();

        foreach ($adjustments as $lineId => $qty) {
            MoveLine::find($lineId)?->update(['qty' => $qty]);
        }

        return $newLines->map(fn (array $attributes) => $this->lines()->create($attributes));
    }

    protected function buildOverflowLines(float $quantity): Collection
    {
        if (float_compare($quantity, 0.0, precisionRounding: $this->uom->rounding) <= 0) {
            return collect();
        }

        if ($this->product->tracking !== ProductTracking::SERIAL) {
            return collect([array_merge($this->buildLineAttributes(quantity: 0), ['qty' => $quantity])]);
        }

        $unitCount = (int) $this->uom->computeQuantity($quantity, $this->product->uom);

        if ($unitCount <= 0) {
            return collect();
        }

        return collect(range(1, $unitCount))
            ->map(fn () => array_merge($this->buildLineAttributes(quantity: 0), [
                'qty'            => 1,
                'product_uom_id' => $this->product->uom->id,
            ]));
    }

    public function computeQuantity()
    {
        $moveLineIds = $this->lines->pluck('id')->all();

        $data = MoveLine::whereIn('id', $moveLineIds)
            ->groupBy('move_id', 'uom_id')
            ->selectRaw('move_id, uom_id, SUM(qty) as qty_sum')
            ->get();

        $sumQty = [];

        foreach ($data as $row) {
            $uom = $this->uom;

            $sumQty[$row->move_id] = ($sumQty[$row->move_id] ?? 0.0) + $row->uom->computeQuantity($row->qty_sum, $uom, round: false);
        }

        $this->quantity = $sumQty[$this->id] ?? 0.0;
    }

    public function lineQuantityTotal()
    {
        return $this->lines()->get()->sum(
            fn (MoveLine $line) => $line->uom->computeQuantity($line->qty, $this->uom, round: false)
        );
    }

    public function split(float $qty, ?int $restrictPartnerId = null): array
    {
        if (in_array($this->state, [MoveState::DONE, MoveState::CANCELED])) {
            throw new \Exception(__('inventories::system.move.split-done-or-cancel'));
        }

        if ($this->state === MoveState::DRAFT) {
            throw new \Exception(__('inventories::system.move.split-draft'));
        }

        if (float_is_zero($qty, precisionRounding: $this->product->uom->rounding)) {
            return [];
        }

        $uomQty = $this->product->uom->computeQuantity($qty, $this->uom, roundingMethod: 'HALF-UP');

        if (
            float_compare(
                $qty,
                $this->uom->computeQuantity($uomQty, $this->product->uom, roundingMethod: 'HALF-UP'),
                precisionDigits: 2
            ) === 0
        ) {
            $defaults = $this->buildSplitAttributes($uomQty);
        } else {
            $defaults = $this->buildSplitAttributes($qty, forceUomId: $this->product->uom_id);
        }

        if ($restrictPartnerId) {
            $defaults['restrict_partner_id'] = $restrictPartnerId;
        }

        $splitAttributes = array_merge($this->toArray(), $defaults, ['id' => null]);

        $remaining = float_round(
            $this->product->uom->computeQuantity(max(0, $this->product_qty - $qty), $this->uom, round: false),
            precisionDigits: 2
        );

        $this->skipUnreserveOnQuantityChange = true;

        $this->update(['product_uom_qty' => $remaining]);

        return $splitAttributes;
    }

    public function buildSplitAttributes(float $qty, ?int $forceUomId = null): array
    {
        $values = [
            'state'                   => MoveState::DRAFT,
            'product_uom_qty'         => $qty,
            'product_qty'             => $this->uom->computeQuantity($qty, $this->product->uom, true, 'HALF-UP'),
            'quantity'                => 0,
            'is_picked'               => false,
            'procure_method'          => $this->procure_method,
            'price_unit'              => $this->price_unit,
            'deadline'                => $this->deadline,
            // 'rule_id'                 => null,
            'origin_returned_move_id' => $this->origin_returned_move_id,
            'move_origin_ids'         => $this->moveOrigins->pluck('id')->all(),
            'move_destination_ids'    => $this->moveDestinations
                ->filter(fn ($x) => ! in_array($x->state, [MoveState::DONE, MoveState::CANCELED]))
                ->pluck('id')
                ->all(),
        ];

        if ($forceUomId) {
            $values['uom_id'] = $forceUomId;
        }

        return $values;
    }

    public function computeState()
    {
        $rounding = $this->uom->rounding;

        if (
            in_array($this->state, [MoveState::CANCELED, MoveState::DONE])
            || ($this->state === MoveState::DRAFT && ! $this->quantity)
        ) {
            return;
        } elseif (float_compare($this->quantity, $this->product_uom_qty, precisionRounding: $rounding) >= 0) {
            $this->state = MoveState::ASSIGNED;
        } elseif ($this->quantity && float_compare($this->quantity, $this->product_uom_qty, precisionRounding: $rounding) <= 0) {
            $this->state = MoveState::PARTIALLY_ASSIGNED;
        } elseif (
            ($this->procure_method === ProcureMethod::MAKE_TO_ORDER && $this->moveOrigins->isEmpty())
            || (
                $this->moveOrigins->isNotEmpty()
                && $this->moveOrigins->some(
                    fn ($orig) => float_compare($orig->product_uom_qty, 0, precisionRounding: $orig->uom->rounding) > 0
                    && ! in_array($orig->state, [MoveState::DONE, MoveState::CANCELED])
                )
            )
        ) {
            $this->state = MoveState::WAITING;
        } else {
            $this->state = MoveState::CONFIRMED;
        }
    }

    public function operationGroupingKey(): array
    {
        $keys = [
            $this->procurement_group_id,
            $this->source_location_id,
            $this->destination_location_id,
            $this->operation_type_id,
        ];

        if ($this->partner_id && ! $this->procurement_group_id) {
            $keys[] = $this->partner_id;
        }

        return $keys;
    }

    public function buildProcurementOptions(): ProcurementOptions
    {
        $warehouse = $this->sourceLocation?->warehouse
            ? ($this->warehouse ?: $this->operationType?->warehouse)
            : $this->rule?->propagateWarehouse;

        return ProcurementOptions::make()
            ->plannedAt($this->scheduled_at)
            ->deadline($this->deadline)
            ->moveDestinations($this->procure_method === ProcureMethod::MAKE_TO_ORDER ? collect([$this]) : collect())
            ->group($this->resolvePropagatedGroup())
            ->routes($this->routes)
            ->warehouse($warehouse)
            ->packaging($this->productPackaging)
            ->linkBomLine($this->bom_line_id);
    }

    protected function resolvePropagatedGroup(): ?ProcurementGroup
    {
        if (! $this->rule) {
            return $this->procurementGroup;
        }

        if ($this->rule->group_propagation_option === GroupPropagation::NONE) {
            return null;
        }

        if ($this->rule->group_propagation_option === GroupPropagation::FIXED && $this->rule->procurement_group_id) {
            return $this->rule->procurementGroup;
        }

        return $this->procurementGroup;
    }

    public function buildLineAttributes($quantity = null, ?ProductQuantity $reservedFrom = null): array
    {
        $attributes = [
            'reference'               => $this->origin,
            'move_id'                 => $this->id,
            'product_id'              => $this->product_id,
            'uom_id'                  => $this->uom_id,
            'source_location_id'      => $this->source_location_id,
            'destination_location_id' => $this->destination_location_id,
            'operation_id'            => $this->operation_id,
            'company_id'              => $this->company_id,
        ];

        if ($quantity) {
            $attributes = array_merge($attributes, $this->lineQuantityAttributes($quantity));
        }

        if ($reservedFrom) {
            $attributes = array_merge($attributes, [
                'source_location_id' => $reservedFrom->location_id,
                'lot_id'             => $reservedFrom->lot_id,
                'package_id'         => $reservedFrom->package?->id,
            ]);
        }

        return $attributes;
    }

    protected function lineQuantityAttributes(float $quantity): array
    {
        $inMoveUom = float_round(
            $this->product->uom->computeQuantity($quantity, $this->uom, roundingMethod: 'HALF-UP'),
            precisionDigits: 2
        );

        $roundTrip = $this->uom->computeQuantity($inMoveUom, $this->product->uom, roundingMethod: 'HALF-UP');

        return float_compare($quantity, $roundTrip, precisionDigits: 2) === 0
            ? ['qty' => $inMoveUom]
            : ['qty' => $quantity, 'uom_id' => $this->product->uom->id];
    }

    public function upstreamAvailability(Collection $reservedMoveIds, Collection $partiallyReservedMoveIds): array
    {
        $received = $this->upstreamReceivedQuantities();

        $claimed = $this->upstreamClaimedQuantities($reservedMoveIds, $partiallyReservedMoveIds);

        $rounding = $this->product->uom->rounding;

        $available = [];

        foreach ($received as $bucket => $quantity) {
            $net = $quantity - ($claimed[$bucket] ?? 0);

            if (float_compare($net, 0, precisionRounding: $rounding) > 0) {
                $available[$bucket] = $net;
            }
        }

        return $available;
    }

    public function upstreamReceivedQuantities(): array
    {
        return $this->moveOrigins
            ->flatMap->moveDestinations
            ->flatMap->moveOrigins
            ->filter(fn (Move $move) => $move->state === MoveState::DONE)
            ->flatMap->lines
            ->groupBy(fn (MoveLine $line) => implode('_', [
                $line->destination_location_id,
                $line->lot_id,
                $line->result_package_id,
            ]))
            ->map(fn (Collection $lines) => $lines->sum(
                fn (MoveLine $line) => $line->uom->computeQuantity($line->qty, $line->product->uom)
            ))
            ->all();
    }

    public function upstreamClaimedQuantities(Collection $reservedMoveIds, Collection $partiallyReservedMoveIds): array
    {
        $siblings = $this->moveOrigins
            ->flatMap->moveDestinations
            ->filter(fn (Move $move) => $move->id !== $this->id);

        $key = fn (MoveLine $line) => implode('_', [$line->source_location_id, $line->lot_id, $line->package_id]);

        $claimed = $siblings
            ->filter(fn (Move $move) => $move->state === MoveState::DONE)
            ->flatMap->lines
            ->groupBy($key)
            ->map(fn (Collection $lines) => $lines->sum(
                fn (MoveLine $line) => $line->uom->computeQuantity($line->qty, $line->product->uom)
            ))
            ->all();

        $reserved = $siblings
            ->filter(fn (Move $move) => in_array($move->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])
                || $reservedMoveIds->contains($move->id)
                || $partiallyReservedMoveIds->contains($move->id))
            ->flatMap->lines
            ->groupBy($key)
            ->map(fn (Collection $lines) => $lines->sum('uom_qty'))
            ->all();

        foreach ($reserved as $bucket => $quantity) {
            $claimed[$bucket] = ($claimed[$bucket] ?? 0.0) + $quantity;
        }

        return $claimed;
    }

    public function reserveFrom(
        float $need,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = true
    ): float {
        $rounding = $this->product->uom->rounding;

        $plan = ProductQuantity::planReservation(
            $this->product,
            $location,
            $need,
            uom: $this->uom,
            lot: $lot,
            package: $package,
            partner: $partner,
            strict: $strict,
        );

        $reusableLines = $this->reusableLinesByBucket();

        $taken = 0.0;

        $newLineAttributes = [];

        foreach ($this->mergePlanByBucket($plan) as $bucket => $entry) {
            $taken += $entry->quantity;

            $line = $reusableLines[$bucket] ?? null;

            $inLineUom = $line
                ? float_round(
                    $this->product->uom->computeQuantity($entry->quantity, $line->uom, roundingMethod: 'HALF-UP'),
                    precisionRounding: $rounding
                )
                : null;

            $roundTrip = $line
                ? $line->uom->computeQuantity($inLineUom, $this->product->uom, roundingMethod: 'HALF-UP')
                : null;

            if ($line && float_compare($entry->quantity, $roundTrip, precisionRounding: $rounding) === 0) {
                $line->update(['qty' => $line->qty + $inLineUom]);

                continue;
            }

            $tracksSerials = $this->product->tracking === ProductTracking::SERIAL
                && ($this->operationType?->use_create_lots || $this->operationType?->use_existing_lots);

            if ($tracksSerials) {
                array_push($newLineAttributes, ...$this->buildSerialLineAttributes($entry->stock, $entry->quantity));
            } else {
                $newLineAttributes[] = $this->buildLineAttributes(quantity: $entry->quantity, reservedFrom: $entry->stock);
            }
        }

        foreach ($newLineAttributes as $attributes) {
            $this->lines()->create($attributes);
        }

        if ($newLineAttributes !== []) {
            $this->load('lines');
        }

        return $taken;
    }

    protected function reusableLinesByBucket(): array
    {
        $buckets = [];

        foreach ($this->lines as $line) {
            if ($line->result_package_id || $this->product->tracking === ProductTracking::SERIAL) {
                continue;
            }

            $bucket = implode('_', [
                $line->source_location_id,
                $line->lot_id,
                $line->package_id,
                $line->partner_id,
            ]);

            $buckets[$bucket] = $line;
        }

        return $buckets;
    }

    protected function mergePlanByBucket(array $plan): array
    {
        $merged = [];

        foreach ($plan as $entry) {
            $bucket = $entry->groupingKey();

            $merged[$bucket] = isset($merged[$bucket])
                ? $merged[$bucket]->add($entry->quantity)
                : $entry;
        }

        return $merged;
    }

    public function buildSerialLineAttributes(ProductQuantity $reservedFrom, float $quantity): array
    {
        return array_map(
            fn () => $this->buildLineAttributes(quantity: 1, reservedFrom: $reservedFrom),
            range(0, (int) $quantity - 1)
        );
    }

    public function consumesStock()
    {
        $fromWarehouse = $this->sourceLocation->warehouse ?? null;

        $toWarehouse = $this->destinationLocation->warehouse ?? null;

        return $this->operationType?->type === OperationTypeEnum::INTERNAL
            || $this->operationType?->type === OperationTypeEnum::OUTGOING
            || $this->operationType?->type === OperationTypeEnum::MANUFACTURE
            || (
                $fromWarehouse
                &&
                $toWarehouse
                && $fromWarehouse->id !== $toWarehouse->id
            );
    }

    public function forecastOutgoing(Collection $moves, Warehouse $warehouse, Location $location): array
    {
        $warehousePath = $warehouse->viewLocation?->parent_path;

        $warehouseLocationIds = $warehousePath
            ? Location::query()->where('parent_path', 'like', $warehousePath.'%')->pluck('id')
            : collect();

        $pendingStates = [MoveState::CONFIRMED, MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED, MoveState::WAITING];

        $incoming = self::query()
            ->where('product_id', $this->product_id)
            ->whereIn('destination_location_id', $warehouseLocationIds)
            ->whereHas('operationType', fn ($query) => $query->where('type', OperationTypeEnum::INCOMING))
            ->whereIn('state', $pendingStates)
            ->orderBy('scheduled_at')
            ->get();

        $outgoing = self::query()
            ->where('product_id', $this->product_id)
            ->where('source_location_id', $location->id)
            ->whereHas('operationType', fn ($query) => $query->where('type', OperationTypeEnum::OUTGOING))
            ->whereIn('state', $pendingStates)
            ->orderBy('scheduled_at')
            ->get();

        $trackedIds = $moves->pluck('id')->all();

        $forecast = array_fill_keys($trackedIds, [false, false]);

        $running = 0.0;

        $timeline = $incoming
            ->map(fn (self $move) => ['incoming' => true, 'move' => $move, 'date' => $move->scheduled_at])
            ->concat($outgoing->map(fn (self $move) => ['incoming' => false, 'move' => $move, 'date' => $move->scheduled_at]))
            ->sortBy('date')
            ->values();

        foreach ($timeline as $entry) {
            $move = $entry['move'];

            if ($entry['incoming']) {
                $running += $move->product_qty;

                continue;
            }

            $running -= $move->product_qty;

            if (! in_array($move->id, $trackedIds)) {
                continue;
            }

            $covered = $running >= 0;

            $forecast[$move->id] = [
                $covered ? $running : -$move->product_qty,
                $covered
                    ? ($incoming
                        ->filter(fn (self $candidate) => $candidate->scheduled_at <= $move->scheduled_at)
                        ->sortByDesc('scheduled_at')
                        ->first()?->scheduled_at ?? false)
                    : false,
            ];
        }

        return $forecast;
    }

    public function forecast(): array
    {
        if (! $this->product?->is_storable) {
            return [$this->product_qty, false];
        }

        if (! $this->consumesStock()) {
            return $this->operationType?->type === OperationTypeEnum::INCOMING
                ? [$this->incomingForecastAvailability(), false]
                : [false, false];
        }

        return match (true) {
            $this->state === MoveState::ASSIGNED => [
                $this->uom->computeQuantity($this->quantity, $this->product->uom, roundingMethod: 'HALF-UP'),
                false,
            ],
            $this->state === MoveState::DRAFT                                                                 => [$this->draftForecastAvailability(), false],
            in_array($this->state, [MoveState::WAITING, MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED]) => $this->pendingForecast(),
            default                                                                                           => [false, false],
        };
    }

    protected function forecastHorizon(): Carbon
    {
        $now = now();

        return max(Carbon::parse($this->scheduled_at ?? $now), $now);
    }

    protected function forecastAt(?int $warehouseId): float
    {
        return Product::withTrashed()->find($this->product_id)
            ->withStockScope(StockScope::make()->forWarehouses($warehouseId)->asOf($this->forecastHorizon()))
            ->stockLevels()
            ->forecast;
    }

    protected function draftForecastAvailability(): float
    {
        $forecast = $this->forecastAt($this->sourceLocation->warehouse_id);

        return float_compare($forecast, $this->product_qty, precisionRounding: $this->product->uom->rounding) >= 0
            ? $forecast
            : $forecast - $this->product_qty;
    }

    protected function incomingForecastAvailability(): float
    {
        $forecast = $this->forecastAt($this->destinationLocation->warehouse_id);

        return $this->state === MoveState::DRAFT
            ? $forecast + $this->product_qty
            : $forecast;
    }

    protected function pendingForecast(): array
    {
        $warehouseId = $this->sourceLocation->warehouse_id;

        if (! $warehouseId) {
            return [false, false];
        }

        return $this->forecastOutgoing(
            collect([$this]),
            Warehouse::find($warehouseId),
            $this->sourceLocation
        )[$this->id];
    }

    public function assertSerialUniqueness(): void
    {
        $trackedStock = ProductQuantity::query()
            ->where('product_id', $this->product_id)
            ->whereIn('location_id', $this->destinationLocation->internalDescendants()->pluck('id')->unique())
            ->whereIn('lot_id', $this->lines->pluck('lot_id')->unique()->filter())
            ->get()
            ->filter(fn (ProductQuantity $stock) => $stock->product->tracking === ProductTracking::SERIAL
                && $stock->location->type !== LocationType::INVENTORY
                && $stock->lot_id);

        if ($trackedStock->isEmpty()) {
            return;
        }

        $duplicates = ProductQuantity::query()
            ->whereIn('product_id', $trackedStock->pluck('product_id')->unique())
            ->whereIn('location_id', $trackedStock->flatMap(
                fn (ProductQuantity $stock) => $stock->location->internalDescendants()->pluck('id')
            )->unique())
            ->whereIn('lot_id', $trackedStock->pluck('lot_id')->unique()->filter())
            ->groupBy('product_id', 'location_id', 'lot_id')
            ->selectRaw('product_id, location_id, lot_id, SUM(quantity) as qty')
            ->with(['product', 'lot'])
            ->get();

        foreach ($duplicates as $duplicate) {
            if (float_compare(abs($duplicate->qty), 1, precisionRounding: $duplicate->product->uom->rounding) > 0) {
                throw new \Exception(__('inventories::system.move.serial-already-assigned', [
                    'product'       => $duplicate->product->name,
                    'serial_number' => $duplicate->lot->name,
                ]));
            }
        }
    }
}
