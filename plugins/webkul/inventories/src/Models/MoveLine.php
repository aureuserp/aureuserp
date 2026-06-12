<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\MoveLineFactory;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class MoveLine extends Model
{
    use HasFactory;

    protected $table = 'inventories_move_lines';

    protected $fillable = [
        'lot_name',
        'state',
        'reference',
        'picking_description',
        'qty',
        'uom_qty',
        'is_picked',
        'scheduled_at',
        'move_id',
        'operation_id',
        'product_id',
        'uom_id',
        'package_id',
        'result_package_id',
        'package_level_id',
        'lot_id',
        'partner_id',
        'source_location_id',
        'destination_location_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'state'        => MoveState::class,
        'qty'          => 'float',
        'uom_qty'      => 'float',
        'is_picked'    => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    protected array $context = [];

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
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

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function resultPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function packageLevel(): BelongsTo
    {
        return $this->belongsTo(PackageLevel::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): MoveLineFactory
    {
        return MoveLineFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($moveLine) {
            $moveLine->creator_id ??= Auth::id();

            $moveLine->company_id ??= $moveLine->move?->company_id;

            $moveLine->computeState();
        });

        static::saving(function ($moveLine) {
            $moveLine->computeOperationId();

            $moveLine->computeReference();

            $moveLine->computeUOMQty();

            $moveLine->computePickingDescription();

            $moveLine->computeProductId();

            $moveLine->computePartnerId();

            $moveLine->computeUOMId();

            $moveLine->computeIsPicked();

            $moveLine->computeSourceLocationId();

            $moveLine->computeDestinationLocationId();

            $moveLine->computeScheduledAt();
        });

        static::created(function ($moveLine) {
            if ($moveLine->state !== MoveState::DONE) {
                $reservation = ! $moveLine->move->shouldBypassReservation();

                if ($moveLine->qty && $reservation) {
                    ProductQuantity::updateReservedQuantity(
                        product: $moveLine->product,
                        location: $moveLine->sourceLocation,
                        quantity: $moveLine->uom_qty,
                        lot: $moveLine->lot,
                        package: $moveLine->package,
                    );
                }
            }

            $moveLine->move->computeQuantity();

            $moveLine->move->computeState();

            $moveLine->move->save();
        });

        static::updating(function ($moveLine) {
            if ($moveLine->product->is_storable && $moveLine->state !== MoveState::DONE) {
                if ($moveLine->isDirty('qty') || $moveLine->isDirty('uom_id')) {
                    $newReservedQty = $moveLine->uom->computeQuantity($moveLine->qty, $moveLine->product->uom, roundingMethod: 'HALF-UP');

                    if (float_compare($newReservedQty, 0, precisionRounding: $moveLine->product->uom->rounding) < 0) {
                        throw new \Exception(__('inventories::system.move-line.negative-quantity-not-allowed'));
                    }
                } else {
                    $newReservedQty = $moveLine->uom_qty;
                }

                if (! float_is_zero($moveLine->getOriginal('uom_qty'), precisionRounding: $moveLine->product->uom->rounding)) {
                    $moveLine->synchronizeQuantity(-$moveLine->getOriginal('uom_qty'), $moveLine->sourceLocation, action: 'reserved');
                }

                if (! $moveLine->move->shouldBypassReservation($moveLine->sourceLocation)) {
                    $moveLine->synchronizeQuantity(
                        $newReservedQty,
                        $moveLine->sourceLocation,
                        action: 'reserved',
                        values: [
                            'lot'     => $moveLine->lot,
                            'package' => $moveLine->package,
                        ]
                    );
                }
            }
        });

        static::updated(function ($moveLine) {
            if ($moveLine->wasChanged('qty') || $moveLine->wasChanged('uom_id')) {
                $moveLine->move->computeQuantity();

                $moveLine->move->computeState();

                $moveLine->move->save();
            }
        });

        static::deleted(function ($moveLine) {
            ProductQuantity::updateReservedQuantity(
                product: $moveLine->product,
                location: $moveLine->sourceLocation,
                quantity: -$moveLine->uom_qty,
                lot: $moveLine->lot,
                package: $moveLine->package,
            );

            $moveLine->move->computeQuantity();

            $moveLine->move->computeState();

            $moveLine->move->save();
        });
    }

    public function computeOperationId()
    {
        $this->operation_id ??= $this->move?->operation_id;
    }

    public function computeState()
    {
        $this->state ??= $this->move?->state;
    }

    public function computeReference()
    {
        $this->reference ??= $this->move?->reference;
    }

    public function computeUOMQty()
    {
        if (! $this->uom) {
            return;
        }

        $this->uom_qty = $this->uom->computeQuantity($this->qty, $this->product->uom, roundingMethod: 'HALF-UP');
    }

    public function computePickingDescription()
    {
        $this->picking_description ??= $this->move?->description_picking;
    }

    public function computePartnerId()
    {
        $this->partner_id ??= $this->move?->partner_id;
    }

    public function computeProductId()
    {
        $this->product_id ??= $this->move?->product_id;
    }

    public function computeUOMId()
    {
        $this->uom_id ??= $this->product?->uom_id;
    }

    public function computeIsPicked()
    {
        $this->is_picked ??= $this->move?->is_picked ?? false;
    }

    public function computeSourceLocationId()
    {
        $this->source_location_id ??= $this->move?->source_location_id;
    }

    public function computeDestinationLocationId()
    {
        $this->destination_location_id ??= $this->move?->destination_location_id;
    }

    public function computeScheduledAt()
    {
        $this->scheduled_at ??= $this->move?->scheduled_at ?? now();
    }

    public function synchronizeQuantity(
        float $quantity,
        Location $location,
        string $action = 'available',
        $incomingDate = null,
        array $values = []
    ): array {
        $lot = $values['lot'] ?? $this->lot;

        $package = $values['package'] ?? $this->package;

        $availableQty = 0;

        if (
            ! $this->product->is_storable
            || float_is_zero($quantity, precisionRounding: $this->uom->rounding)
        ) {
            return [0, false];
        }

        if ($action === 'available') {
            [$availableQty, $incomingDate] = ProductQuantity::updateAvailableQuantity(
                product: $this->product,
                location: $location,
                quantity: $quantity,
                lot: $lot,
                package: $package,
                incomingDate: $incomingDate,
            );
        } elseif ($action === 'reserved' && ! $this->move->shouldBypassReservation($location)) {
            ProductQuantity::updateReservedQuantity(
                product: $this->product,
                location: $location,
                quantity: $quantity,
                lot: $lot,
                package: $package
            );
        }

        if ($availableQty < 0 && $lot) {
            $untrackedQty = ProductQuantity::getAvailableQuantity(
                product: $this->product,
                location: $location,
                lot: null,
                package: $package,
                strict: true
            );

            if (! $untrackedQty) {
                return [$availableQty, $incomingDate];
            }

            $takenFromUntrackedQty = min($untrackedQty, abs($quantity));

            ProductQuantity::updateAvailableQuantity(
                product: $this->product,
                location: $location,
                quantity: -$takenFromUntrackedQty,
                lot: null,
                package: $package,
                incomingDate: $incomingDate,
            );

            ProductQuantity::updateAvailableQuantity(
                product: $this->product,
                location: $location,
                quantity: $takenFromUntrackedQty,
                lot: $lot,
                package: $package,
                incomingDate: $incomingDate,
            );
        }

        return [$availableQty, $incomingDate];
    }

    public function freeReservation(
        Product $product,
        Location $location,
        float $quantity,
        ?Lot $lot = null,
        ?Package $package = null,
        $moveLineIdsToIgnore = null
    ): void {
        $moveLineIdsToIgnore = $moveLineIdsToIgnore ?? collect();

        $moveLineIdsToIgnore->push($this->id);

        if ($this->move->shouldBypassReservation($location)) {
            return;
        }

        $outdatedMoveLines = MoveLine::query()
            ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
            ->where('product_id', $product->id)
            ->where('lot_id', $lot?->id)
            ->where('source_location_id', $location->id)
            ->where('package_id', $package?->id)
            ->where('uom_qty', '>', 0.0)
            ->where('is_picked', false)
            ->whereNotIn('id', $moveLineIdsToIgnore->all())
            ->get()
            ->sortBy(function ($candidate) {
                $isCurrentOperation = $candidate->move->operation_id !== $this->move->operation_id ? 1 : 0;

                $scheduledAt = $candidate->operation_id
                    ? ($candidate->move->operation->scheduled_at ?? $candidate->move->scheduled_at)
                    : ($candidate->move->scheduled_at ?? null);

                return [
                    $isCurrentOperation,
                    $scheduledAt ? -$scheduledAt->timestamp() : 0,
                    -$candidate->id,
                ];
            });

        $moveToReassign = collect();

        $toDeleteCandidateIds = collect();

        $rounding = $this->uom->rounding;

        foreach ($outdatedMoveLines as $candidate) {
            $moveToReassign->push($candidate->move);

            if (float_compare($candidate->uom_qty, $quantity, precisionRounding: $rounding) <= 0) {
                $quantity -= $candidate->uom_qty;

                $toDeleteCandidateIds->push($candidate->id);

                if (float_is_zero($quantity, precisionRounding: $rounding)) {
                    break;
                }
            } else {
                $candidate->update([
                    'qty' => $candidate->qty - $candidate->product->uom->computeQuantity($quantity, $candidate->uom, roundingMethod: 'HALF-UP'),
                ]);

                break;
            }
        }

        $moveLinesToDelete = MoveLine::whereIn('id', $toDeleteCandidateIds)->get();

        $moveLinesToDelete->pluck('move')
            ->merge($moveToReassign)
            ->unique('id')
            ->each(function ($move) {
                $move->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);

                $move->moveOrigins()->detach();
            });

        MoveLine::whereIn('id', $toDeleteCandidateIds)->get()->each(fn ($moveLine) => $moveLine->delete());

        InventoryFacade::assignMoves($moveToReassign->unique('id'));
    }

    public function createAndAssignProductionLot()
    {
        $lotVals = [$this->prepareNewLotVals()];

        if ($this->product->tracking === ProductTracking::LOT) {
            $existingLot = Lot::where('product_id', $this->product_id)
                ->where('name', $this->lot_name)
                ->first();

            if ($existingLot) {
                $this->update(['lot_id' => $existingLot->id]);

                return;
            }
        }

        $lot = Lot::create($lotVals[0]);

        $this->update(['lot_id' => $lot->id]);
    }

    public function prepareNewLotVals(): array
    {
        return [
            'name'       => $this->lot_name,
            'product_id' => $this->product_id,
            'company_id' => $this->company_id,
        ];
    }
}
