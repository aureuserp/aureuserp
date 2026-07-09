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
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Models\OrderLine as PurchaseOrderLine;
use Webkul\Sale\Models\OrderLine as SaleOrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class Move extends Model
{
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

    protected array $context = [];

    public static array $globalContext = [];

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function getContext(string $key, $default = null)
    {
        return $this->context[$key] ?? $default;
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
        return $this->hasMany(MoveLine::class);
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

    public function shouldBypassReservation($forceLocation = null): bool
    {
        $location = $forceLocation ?? $this->sourceLocation;

        return $location->shouldBypassReservation() || ! $this->product->is_storable;
    }

    public function skipPush()
    {
        return $this->is_inventory
            || (
                $this->moveDestinations->isNotEmpty()
                && $this->moveDestinations->some(fn ($move) => $move->sourceLocation->isChildOf($this->destinationLocation))
            )
            || (
                $this->final_location_id
                && $this->finalLocation->isChildOf($this->destinationLocation)
            );
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

    public function shouldBeAssigned()
    {
        return ! $this->operation_id and $this->operation_type_id;
    }

    public function getForecastAvailabilityAttribute()
    {
        [$forecastAvailability] = $this->getForecastInformation();

        return $forecastAvailability;
    }

    public function getForecastExpectedDateAttribute()
    {
        [, $forecastExpectedDate] = $this->getForecastInformation();

        return $forecastExpectedDate;
    }

    protected static function newFactory(): MoveFactory
    {
        return MoveFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($move) {
            $move->creator_id ??= Auth::id();

            $move->company_id ??= $move->operation?->company_id ?? $move->operationType?->company_id;

            $move->state ??= MoveState::DRAFT;

            $skipAdditional = static::$globalContext['skip_additional'] ?? true;

            static::$globalContext = [];

            $move->setContext(['skip_additional' => $skipAdditional]);

            if ($move->operation && ! $skipAdditional) {
                if ($move->operation->state === OperationState::DONE) {
                    $move->state = MoveState::DONE;

                    $move->additional = true;
                } elseif (! in_array($move->operation->state, [
                    OperationState::DRAFT,
                    OperationState::DONE,
                    OperationState::CANCELED
                ])) {
                    $move->additional = true;
                }
            }
        });

        static::created(function ($move) {
            if ($move->getContext('skip_additional', true) || ! $move->additional) {
                return;
            }

            $move->operation->autoConfirm();
        });

        static::saving(function ($move) {
            $move->computeWarehouseId();

            $move->computeName();

            $move->computeReference();

            $move->computeUOMId();

            $move->computeProductQty();

            $move->computeProductUOMQty();

            $move->computeProcureMethod();

            $move->computePartnerId();

            $move->computeOperationTypeId();

            $move->computeSourceLocationId();

            $move->computeDestinationLocationId();

            $move->computeScheduledAt();
        });

        static::updated(function ($move) {
            if ($move->wasChanged('quantity')) {
                $move->setQuantity();
            }

            $receiptMovesToReassign = collect();

            $moveToRecomputeState = collect();

            if ($move->wasChanged('product_uom_qty')) {
                if (! ($move->context['do_not_unreserve'] ?? false)) {
                    $shouldUnreserve = ! in_array($move->state, [MoveState::DRAFT, MoveState::DONE, MoveState::CANCELED])
                        && float_compare($move->quantity, $move->product_uom_qty ?? null, precisionRounding: $move->uom->rounding) === 1;

                    if ($shouldUnreserve) {
                        InventoryFacade::unreserveMoves(collect([$move]));

                        if ($move->sourceLocation->type === LocationType::SUPPLIER) {
                            $receiptMovesToReassign->push($move->refresh());
                        }
                    } else {
                        if ($move->state === MoveState::ASSIGNED) {
                            $move->update(['state' => MoveState::PARTIALLY_ASSIGNED]);
                        }

                        if (
                            $move->sourceLocation->type === LocationType::SUPPLIER
                            && in_array($move->state, [MoveState::PARTIALLY_ASSIGNED, MoveState::ASSIGNED])
                        ) {
                            $receiptMovesToReassign->push($move);
                        } else {
                            $moveToRecomputeState->push($move);
                        }
                    }
                }
            }

            if ($move->wasChanged('state')) {
                $move->lines->each(fn ($moveLine) => $moveLine->update(['state' => $moveLine->move->refresh()->state]));

                if ($operation = $move->operation) {
                    $operation->refresh();

                    $operation->computeState();

                    $operation->save();
                }
            }

            if ($move->wasChanged('is_picked')) {
                $move->lines()->get()->each(fn ($moveLine) => $moveLine->update(['is_picked' => $move->is_picked]));
            }

            if ($move->wasChanged('destination_location_id')) {
                // TODO: apply putaway rules
            }

            if ($receiptMovesToReassign->isNotEmpty()) {
                InventoryFacade::assignMoves($receiptMovesToReassign);
            }
        });

        static::deleting(function ($move) {
            $move->lines->each->delete();
        });
    }

    public function computeWarehouseId()
    {
        $this->warehouse_id ??= $this->operation?->destinationLocation->warehouse_id;
    }

    public function computeName()
    {
        $this->name ??= $this->product->name;
    }

    public function computeReference()
    {
        $this->reference ??= $this->operation?->name;
    }

    public function computeProductQty()
    {
        $this->product_qty ??= $this->uom?->computeQuantity($this->product_uom_qty, $this->product->uom, roundingMethod: 'HALF-UP');
    }

    public function computeProductUOMQty()
    {
        $this->product_uom_qty ??= $this->product->uom?->computeQuantity($this->product_qty, $this->uom, roundingMethod: 'HALF-UP');
    }

    public function computeProcureMethod()
    {
        $this->procure_method ??= ProcureMethod::MAKE_TO_STOCK;
    }

    public function computeUOMId()
    {
        $this->uom_id ??= $this->product?->uom_id;
    }

    public function computePartnerId()
    {
        $this->partner_id ??= $this->operation?->partner_id;
    }

    public function computeOperationTypeId()
    {
        $this->operation_type_id ??= $this->operation?->operation_type_id;
    }

    public function computeSourceLocationId()
    {
        $this->source_location_id ??= $this->operation?->source_location_id ?? $this->operationType?->source_location_id;
    }

    public function computeDestinationLocationId()
    {
        $this->destination_location_id ??= $this->operation?->destination_location_id ?? $this->operationType?->destination_location_id;
    }

    public function computeScheduledAt()
    {
        $this->scheduled_at ??= $this->operation?->scheduled_at ?? now();
    }

    public function prepareProcurementOrigin()
    {
        return $this->procurementGroup?->name ?? ($this->origin ?: $this->operation?->name ?: '/');
    }

    public function getPickedQuantity()
    {
        return $this->lines->where('is_picked', true)->sum(function ($moveLine) {
            return $moveLine->uom->computeQuantity($moveLine->qty, $this->uom, roundingMethod: 'HALF-UP');
        });
    }

    public function adjustProcureMethod($operationTypeCode = false)
    {
        $filters = [
            'source_location_id'      => $this->source_location_id,
            'destination_location_id' => $this->destination_location_id,
            'action'                  => ['!=', RuleAction::PUSH],
        ];

        if ($operationTypeCode) {
            $filters['operationType.type'] = $operationTypeCode;
        }

        $rule = InventoryFacade::searchRule(collect(), $this->productPackaging, $this->product, $this->warehouse, $filters);

        if (! $rule) {
            $this->procure_method = ProcureMethod::MAKE_TO_STOCK;

            return;
        }

        $this->rule_id = $rule->id;

        if (in_array($rule->procure_method, [ProcureMethod::MAKE_TO_STOCK, ProcureMethod::MAKE_TO_ORDER])) {
            $this->procure_method = $rule->procure_method;
        } else {
            $this->procure_method = ProcureMethod::MAKE_TO_STOCK;
        }
    }

    public function setQuantity()
    {
        $processDecrease = function (Move $move, float $quantity) {
            $toDelete = collect();

            foreach ($move->lines->sortBy('id')->reverse() as $moveLine) {
                if (float_is_zero($quantity, precisionRounding: $move->uom->rounding)) {
                    break;
                }

                $qtyMlDec = min($moveLine->qty, $moveLine->uom->computeQuantity($quantity, $moveLine->uom, round: false));

                if (float_is_zero($qtyMlDec, precisionRounding: $moveLine->uom->rounding)) {
                    continue;
                }

                if (
                    float_compare($moveLine->qty, $qtyMlDec, precisionRounding: $moveLine->uom->rounding) === 0
                    && ! in_array($moveLine->state, [MoveState::DONE, MoveState::CANCELED])
                ) {
                    $toDelete->push($moveLine->id);
                } else {
                    $moveLine->update(['qty' => $moveLine->qty - $qtyMlDec]);
                }

                $quantity -= $move->uom->computeQuantity($qtyMlDec, $move->uom, round: false);
            }

            MoveLine::whereIn('id', $toDelete)->get()->each->delete();
        };

        $processIncrease = function (Move $move, float $quantity) {
            $move->setQuantityDone($move->quantity);
        };

        $errors = [];

        $uomQty = float_round($this->quantity, precisionRounding: $this->uom->rounding, roundingMethod: 'HALF-UP');

        $qty = float_round($this->quantity, precisionDigits: 2, roundingMethod: 'HALF-UP');

        if (float_compare($uomQty, $qty, precisionDigits: 2) !== 0) {
            $errors[] = __('The quantity done for the product :product doesn\'t respect the rounding precision defined on the unit of measure :unit. Please change the quantity done or the rounding precision of your unit of measure.', [
                'product' => $this->product->name,
                'unit'    => $this->uom->name,
            ]);
        } else {
            $deltaQty = $this->quantity - $this->getQuantitySml();

            if (float_compare($deltaQty, 0, precisionRounding: $this->uom->rounding) > 0) {
                $processIncrease($this, $deltaQty);
            } elseif (float_compare($deltaQty, 0, precisionRounding: $this->uom->rounding) < 0) {
                $processDecrease($this, abs($deltaQty));
            }
        }

        if (! empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }
    }

    public function setQuantityDone(float $quantity)
    {
        $this->setQuantityDonePrepareVals($quantity);
    }

    public function setQuantityDonePrepareVals(float $qty)
    {
        $toDelete = collect();

        $toUpdate = [];

        $toCreate = [];

        foreach ($this->lines as $moveLine) {
            $moveLineQty = $moveLine->qty;

            if (float_is_zero($qty, precisionRounding: $this->uom->rounding)) {
                $toDelete->push($moveLine->id);

                continue;
            }

            if (float_compare($moveLineQty, 0, precisionRounding: $moveLine->uom->rounding) <= 0) {
                continue;
            }

            if ($moveLine->uom->id !== $this->uom->id) {
                $moveLineQty = $moveLine->uom->computeQuantity($moveLineQty, $this->uom, round: false);
            }

            $takenQty = min($qty, $moveLineQty);

            if ($moveLine->uom->id !== $this->uom->id) {
                $takenQty = $this->uom->computeQuantity($takenQty, $moveLine->uom, round: false);
            }

            $takenQty = float_round($takenQty, precisionRounding: $moveLine->uom->rounding);

            $toUpdate[] = ['id' => $moveLine->id, 'qty' => $takenQty];

            if ($moveLine->uom->id !== $this->uom->id) {
                $takenQty = $moveLine->uom->computeQuantity($takenQty, $this->uom, round: false);
            }

            $qty -= $takenQty;
        }

        if (float_compare($qty, 0.0, precisionRounding: $this->uom->rounding) > 0) {
            if ($this->product->tracking !== ProductTracking::SERIAL) {
                $vals = $this->prepareLineValues(quantity: 0);

                $vals['qty'] = $qty;

                $toCreate[] = $vals;
            } else {
                $uomQty = $this->uom->computeQuantity($qty, $this->product->uom);

                for ($i = 0; $i < (int) $uomQty; $i++) {
                    $vals = $this->prepareLineValues(quantity: 0);

                    $vals['qty'] = 1;

                    $vals['product_uom_id'] = $this->product->uom->id;

                    $toCreate[] = $vals;
                }
            }
        }

        MoveLine::whereIn('id', $toDelete)->get()->each->delete();

        foreach ($toUpdate as $update) {
            $moveLine = MoveLine::find($update['id']);

            $moveLine->update(['qty' => $update['qty']]);
        }

        $newMoveLines = collect();

        foreach ($toCreate as $vals) {
            $moveLine = $this->lines()->create($vals);

            $newMoveLines->push($moveLine);
        }

        return $newMoveLines;
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

    public function getQuantitySml()
    {
        $quantity = 0;

        $this->lines()->get()->each(function ($moveLine) use (&$quantity) {
            $quantity += $moveLine->uom->computeQuantity($moveLine->qty, $this->uom, round: false);
        });

        return $quantity;
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
            $defaults = $this->prepareMoveSplitVals($uomQty);
        } else {
            $defaults = $this->prepareMoveSplitVals($qty, forceUomId: $this->product->uom_id);
        }

        if ($restrictPartnerId) {
            $defaults['restrict_partner_id'] = $restrictPartnerId;
        }

        if ($this->context['source_location_id'] ?? false) {
            $defaults['destination_location_id'] = $this->context['source_location_id'];
        }

        $newMoveVals = array_merge(
            $this->toArray(),
            $defaults,
            ['id' => null]
        );

        $newProductQty = $this->product->uom->computeQuantity(
            max(0, $this->product_qty - $qty),
            $this->uom,
            round: false
        );

        $newProductQty = float_round($newProductQty, precisionDigits: 2);

        $this->setContext(['do_not_unreserve' => true]);

        $this->update(['product_uom_qty' => $newProductQty]);

        return $newMoveVals;
    }

    public function prepareMoveSplitVals(float $qty, ?int $forceUomId = null): array
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

    public function keyAssignOperation(): array
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

        if ($this->created_order_id) {
            $keys[] = $this->created_order_id;
        }

        return $keys;
    }

    public function prepareProcurementValues(): array
    {
        $procurementGroup = $this->procurementGroup ?: false;

        if ($this->rule) {
            if (
                $this->rule->group_propagation_option === GroupPropagation::FIXED
                && $this->rule->procurement_group_id
            ) {
                $procurementGroup = $this->rule->procurementGroup;
            } elseif ($this->rule->group_propagation_option === GroupPropagation::NONE) {
                $procurementGroup = false;
            }
        }

        $datesInfo = [
            'planned' => $this->scheduled_at,
        ];

        if (
            $this->sourceLocation?->warehouse?->lotStock?->parent_path
            && str_contains(
                $this->sourceLocation->parent_path ?? '',
                $this->sourceLocation->warehouse->lotStock->parent_path
            )
        ) {
            $datesInfo = $this->product->getDatesInfo(
                $this->date,
                $this->sourceLocation,
                $this->routes
            );
        }

        $warehouse = $this->warehouse ?: $this->operationType?->warehouse;

        if (! $this->sourceLocation?->warehouse) {
            $warehouse = $this->rule?->propagateWarehouse;
        }

        $moveDestinations = collect();

        if ($this->procure_method === ProcureMethod::MAKE_TO_ORDER) {
            $moveDestinations = collect([$this]);
        }

        $values = [
            'planned'           => $datesInfo['planned'] ?? null,
            'ordered_at'        => $datesInfo['ordered_at'] ?? null,
            'deadline'          => $this->deadline,
            'move_destinations' => $moveDestinations,
            'procurement_group' => $procurementGroup,
            'routes'            => $this->routes,
            'warehouse'         => $warehouse,
            'order_point'       => $this->orderPoint,
            'product_packaging' => $this->productPackaging,
        ];

        if ($this->bom_line_id) {
            $values['bom_line_id'] = $this->bom_line_id;
        }

        return $values;
    }

    public function prepareLineValues($quantity = null, $reservedQuantity = null): array
    {
        $values = [
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
            $uomQuantity = $this->product->uom->computeQuantity(
                $quantity,
                $this->uom,
                roundingMethod: 'HALF-UP'
            );

            $uomQuantity = float_round($uomQuantity, precisionDigits: 2);

            $uomQuantityBackToProductUom = $this->uom->computeQuantity(
                $uomQuantity,
                $this->product->uom,
                roundingMethod: 'HALF-UP'
            );

            if (float_compare($quantity, $uomQuantityBackToProductUom, precisionDigits: 2) === 0) {
                $values = array_merge($values, [
                    'qty' => $uomQuantity,
                ]);
            } else {
                $values = array_merge($values, [
                    'qty'    => $quantity,
                    'uom_id' => $this->product->uom->id,
                ]);
            }
        }

        $package = null;

        if ($reservedQuantity) {
            $package = $reservedQuantity->package;

            $values = array_merge($values, [
                'source_location_id' => $reservedQuantity->location_id,
                'lot_id'             => $reservedQuantity->lot_id,
                'package_id'         => $package?->id,
            ]);
        }

        return $values;
    }

    public function getAvailableMoveLines(Collection $assignedMovesIds, Collection $partiallyAssignedMovesIds): array
    {
        $groupedMoveLinesIn = $this->getAvailableMoveLinesIn();

        $groupedMoveLinesOut = $this->getAvailableMoveLinesOut($assignedMovesIds, $partiallyAssignedMovesIds);

        $rounding = $this->product->uom->rounding;

        $availableMoveLines = [];

        foreach ($groupedMoveLinesIn as $key => $quantity) {
            $net = $quantity - ($groupedMoveLinesOut[$key] ?? 0);

            if (float_compare($net, 0, precisionRounding: $rounding) > 0) {
                $availableMoveLines[$key] = $net;
            }
        }

        return $availableMoveLines;
    }

    public function getAvailableMoveLinesIn(): array
    {
        $groupedMoveLinesIn = [];

        $moveLines = $this->moveOrigins
            ->flatMap->moveDestinations
            ->flatMap->moveOrigins
            ->filter(fn (Move $m) => $m->state === MoveState::DONE)
            ->flatMap->lines;

        $grouped = $moveLines->groupBy(fn ($ml) => implode('_', [
            $ml->destination_location_id,
            $ml->lot_id,
            $ml->result_package_id,
        ]));

        foreach ($grouped as $key => $lines) {
            $quantity = 0.0;

            foreach ($lines as $ml) {
                $quantity += $ml->uom->computeQuantity($ml->qty, $ml->product->uom);
            }

            $groupedMoveLinesIn[$key] = $quantity;
        }

        return $groupedMoveLinesIn;
    }

    public function getAvailableMoveLinesOut(Collection $assignedMovesIds, Collection $partiallyAssignedMovesIds): array
    {
        $groupedMoveLinesOut = [];

        $siblingsOutMoves = $this->moveOrigins
            ->flatMap->moveDestinations
            ->filter(fn (Move $m) => $m->id !== $this->id);

        $keyFn = fn ($ml) => implode('_', [$ml->source_location_id, $ml->lot_id, $ml->package_id]);

        $doneMoveLines = $siblingsOutMoves
            ->filter(fn (Move $m) => $m->state === MoveState::DONE)
            ->flatMap->lines;

        foreach ($doneMoveLines->groupBy($keyFn) as $key => $lines) {
            $quantity = 0.0;

            foreach ($lines as $ml) {
                $quantity += $ml->uom->computeQuantity($ml->qty, $ml->product->uom);
            }

            $groupedMoveLinesOut[$key] = ($groupedMoveLinesOut[$key] ?? 0.0) + $quantity;
        }

        $reservedMoveLines = $siblingsOutMoves
            ->filter(fn (Move $m) => in_array($m->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])
                || $assignedMovesIds->contains($m->id)
                || $partiallyAssignedMovesIds->contains($m->id)
            )
            ->flatMap->lines;

        foreach ($reservedMoveLines->groupBy($keyFn) as $key => $lines) {
            $groupedMoveLinesOut[$key] = ($groupedMoveLinesOut[$key] ?? 0.0) + $lines->sum('uom_qty');
        }

        return $groupedMoveLinesOut;
    }

    public function updateReservedQuantity(
        float $need,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = true
    ): float {
        $rounding = $this->product->uom->rounding;

        $quants = ProductQuantity::getReserveQuantity(
            $this->product,
            $location,
            $need,
            productPackaging: $this->productPackaging,
            uom: $this->uom,
            lot: $lot,
            package: $package,
            partner: $partner,
            strict: $strict,
        );

        $takenQuantity = 0.0;

        $candidateLines = [];

        foreach ($this->lines as $line) {
            if ($line->result_package_id || $this->product->tracking === ProductTracking::SERIAL) {
                continue;
            }

            $candidateKey = implode('_', [
                $line->source_location_id,
                $line->lot_id,
                $line->package_id,
                $line->partner_id,
            ]);

            $candidateLines[$candidateKey] = $line;
        }

        $groupedQuants = [];

        foreach ($quants as [$quant, $quantity]) {
            $groupKey = implode('_', [
                $quant->location_id,
                $quant->lot_id,
                $quant->package_id,
                $quant->partner_id,
            ]);

            if (! isset($groupedQuants[$groupKey])) {
                $groupedQuants[$groupKey] = [$quant, $quantity];
            } else {
                $groupedQuants[$groupKey][1] += $quantity;
            }
        }

        $moveLineVals = [];

        foreach ($groupedQuants as [$reservedQuant, $quantity]) {
            $takenQuantity += $quantity;

            $candidateKey = implode('_', [
                $reservedQuant->location_id,
                $reservedQuant->lot_id,
                $reservedQuant->package_id,
                $reservedQuant->partner_id,
            ]);

            $toUpdate = $candidateLines[$candidateKey] ?? null;

            if ($toUpdate) {
                $uomQuantity = $this->product->uom->computeQuantity($quantity, $toUpdate->uom, roundingMethod: 'HALF-UP');

                $uomQuantity = float_round($uomQuantity, precisionRounding: $rounding);

                $uomQuantityBackToProductUom = $toUpdate->uom->computeQuantity($uomQuantity, $this->product->uom, roundingMethod: 'HALF-UP');
            }

            if ($toUpdate && float_compare($quantity, $uomQuantityBackToProductUom, precisionRounding: $rounding) === 0) {
                $toUpdate->update(['uom_qty' => $toUpdate->uom_qty + $uomQuantity]);
            } else {
                if (
                    $this->product->tracking === ProductTracking::SERIAL
                    && ($this->operationType?->use_create_lots || $this->operationType?->use_existing_lots)
                ) {
                    array_push($moveLineVals, ...$this->addSerialMoveLineToValsList($reservedQuant, $quantity));
                } else {
                    $moveLineVals[] = $this->prepareLineValues(quantity: $quantity, reservedQuantity: $reservedQuant);
                }
            }
        }

        if (! empty($moveLineVals)) {
            foreach ($moveLineVals as $vals) {
                $this->lines()->create($vals);
            }
        }

        return $takenQuantity;
    }

    public function addSerialMoveLineToValsList(ProductQuantity $reservedQuant, float $quantity): array
    {
        return array_map(
            fn () => $this->prepareLineValues(quantity: 1, reservedQuantity: $reservedQuant),
            range(0, (int) $quantity - 1)
        );
    }

    public static function getRelevantStateAmongMoves($moves): \BackedEnum
    {
        return InventoryFacade::getRelevantStateAmongMoves($moves);
    }

    public function isConsuming()
    {
        $fromWarehouse = $this->sourceLocation->warehouse ?? null;

        $toWarehouse = $this->destinationLocation->warehouse ?? null;

        return $this->operationType?->type === OperationTypeEnum::OUTGOING
            || $this->operationType?->type === OperationTypeEnum::MANUFACTURE
            || (
                $fromWarehouse
                &&
                $toWarehouse
                && $fromWarehouse->id !== $toWarehouse->id
            );
    }

    public function getForecastAvailabilityOutgoing(Collection $moves, Warehouse $warehouse, Location $location): array
    {
        $viewLocationParentPath = $warehouse->viewLocation->parent_path;

        $warehouseLocationIds = Location::query()
            ->where('parent_path', 'like', $viewLocationParentPath.'%')
            ->pluck('id');

        $productId = $this->product_id;

        $stockLocationId = $location->id;

        $incomingMoves = self::query()
            ->where('product_id', $productId)
            ->whereIn('destination_location_id', $warehouseLocationIds)
            ->whereHas('operationType', fn ($q) => $q->where('type', OperationTypeEnum::INCOMING))
            ->whereIn('state', [MoveState::CONFIRMED, MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED, MoveState::WAITING])
            ->orderBy('scheduled_at')
            ->get();

        $outgoingMoveIds = $moves->pluck('id')->all();

        $outgoingMoves = self::query()
            ->where('product_id', $productId)
            ->where('source_location_id', $stockLocationId)
            ->whereHas('operationType', fn ($q) => $q->where('type', OperationTypeEnum::OUTGOING))
            ->whereIn('state', [MoveState::CONFIRMED, MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED, MoveState::WAITING])
            ->orderBy('scheduled_at')
            ->get()
            ->keyBy('id');

        $result = [];

        foreach ($outgoingMoveIds as $moveId) {
            $result[$moveId] = [false, false];
        }

        $cumulative = 0.0;

        $timeline = $incomingMoves
            ->map(fn (self $m) => ['type' => OperationTypeEnum::INCOMING, 'move' => $m, 'date' => $m->scheduled_at])
            ->concat(
                $outgoingMoves->values()->map(fn (self $m) => ['type' => OperationTypeEnum::OUTGOING, 'move' => $m, 'date' => $m->scheduled_at])
            )
            ->sortBy('date')
            ->values();

        foreach ($timeline as $entry) {
            $move = $entry['move'];

            if ($entry['type'] === OperationTypeEnum::INCOMING) {
                $cumulative += $move->product_qty;
            } else {
                $cumulative -= $move->product_qty;

                if (! in_array($move->id, $outgoingMoveIds)) {
                    continue;
                }

                $replenishmentFilled = $cumulative >= 0;

                $qtyExpected = $replenishmentFilled
                    ? $cumulative
                    : -$move->product_qty;

                $dateExpected = false;

                if ($replenishmentFilled) {
                    $coveringIncoming = $incomingMoves
                        ->filter(fn (self $in) => $in->scheduled_at <= $move->scheduled_at)
                        ->sortByDesc('scheduled_at')
                        ->first();

                    if ($coveringIncoming) {
                        $dateExpected = $coveringIncoming->scheduled_at;
                    }
                }

                $result[$move->id] = [$qtyExpected, $dateExpected];
            }
        }

        return $result;
    }

    public function getForecastInformation(): array
    {
        $forecastAvailability = false;

        $forecastExpectedDate = false;

        if (! $this->product->is_storable) {
            return [
                $this->product_qty,
                false,
            ];
        }

        $now = now();

        $keyVirtualAvailable = fn (bool $incoming = false) => [
            $incoming ? $this->destinationLocation->warehouse_id : $this->sourceLocation->warehouse_id,
            max(Carbon::parse($this->scheduled_at ?? $now), $now),
        ];

        if ($this->isConsuming()) {
            if ($this->state === MoveState::ASSIGNED) {
                $forecastAvailability = $this->uom->computeQuantity(
                    $this->quantity,
                    $this->product->uom,
                    roundingMethod: 'HALF-UP'
                );
            } elseif ($this->state === MoveState::DRAFT) {
                $key = $keyVirtualAvailable();

                $product = Product::find($this->product_id);

                $product->setContext(['to_date' => Carbon::parse($key[1])]);

                $virtualAvailable = $product->computeQuantities()['virtual_available_qty'] ?? 0;

                $forecastAvailability = $virtualAvailable - $this->product_qty;
            } elseif (in_array($this->state, [MoveState::WAITING, MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED])) {
                $warehouseId = $this->sourceLocation->warehouse_id;

                if ($warehouseId) {
                    $warehouse = Warehouse::find($warehouseId);

                    $location = $this->sourceLocation;

                    $forecastInfo = $this->getForecastAvailabilityOutgoing(collect([$this]), $warehouse, $location);

                    [$forecastAvailability, $forecastExpectedDate] = $forecastInfo[$this->id];
                }
            }
        } elseif ($this->operationType?->type === OperationTypeEnum::INCOMING) {
            $key = $keyVirtualAvailable(incoming: true);

            $product = Product::find($this->product_id);

            $product->setContext(['to_date' => Carbon::parse($key[1])]);

            $forecastAvailability = $product->computeQuantities()['virtual_available_qty'] ?? 0;

            if ($this->state === MoveState::DRAFT) {
                $forecastAvailability += $this->product_qty;
            }
        }

        return [
            $forecastAvailability,
            $forecastExpectedDate,
        ];
    }

    public function checkQuantity()
    {
        $locationIds = $this->destinationLocation->getInternalChildLocations()->pluck('id')->unique()->all();

        $quantities = ProductQuantity::where('product_id', $this->product_id)
            ->whereIn('location_id', $locationIds)
            ->whereIn('lot_id', $this->lines->pluck('lot_id')->unique()->filter()->all())
            ->get();
        
        $serialNumberQuantities = $quantities->filter(
            fn ($quantity) => $quantity->product->tracking === ProductTracking::SERIAL
                && $quantity->location->type !== LocationType::INVENTORY
                && $quantity->lot_id
        );

        if ($serialNumberQuantities->isEmpty()) {
            return;
        }

        $locationIds = $serialNumberQuantities->flatMap(
            fn ($quantity) => $quantity->location->getInternalChildLocations()->pluck('id')
        )->unique()->all();

        $groups = ProductQuantity::whereIn('product_id', $serialNumberQuantities->pluck('product_id')->unique()->all())
            ->whereIn('location_id', $locationIds)
            ->whereIn('lot_id', $serialNumberQuantities->pluck('lot_id')->unique()->filter()->all())
            ->groupBy('product_id', 'location_id', 'lot_id')
            ->selectRaw('product_id, location_id, lot_id, SUM(quantity) as qty')
            ->with(['product', 'lot'])
            ->get();

        foreach ($groups as $group) {
            if (float_compare(abs($group->qty), 1, precisionRounding: $group->product->uom->rounding) > 0) {
                throw new \Exception(__('inventories::system.move.serial-already-assigned', [
                    'product'       => $group->product->name,
                    'serial_number' => $group->lot->name,
                ]));
            }
        }
    }
}
