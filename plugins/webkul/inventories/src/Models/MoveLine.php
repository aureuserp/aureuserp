<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
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
use Webkul\Support\Traits\BelongsToCompany;

class MoveLine extends Model
{
    use BelongsToCompany;
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

        static::creating(function (MoveLine $line) {
            $line->creator_id ??= Auth::id();

            $line->company_id ??= $line->move?->company_id;

            $line->state ??= $line->move?->state;
        });

        static::saving(function (MoveLine $line) {
            $line->applyDefaults();
        });

        static::created(function (MoveLine $line) {
            if (
                $line->state !== MoveState::DONE
                && $line->qty
                && ! $line->move->bypassesReservation()
            ) {
                ProductQuantity::applyReservationDelta(
                    product: $line->product,
                    location: $line->sourceLocation,
                    delta: $line->uom_qty,
                    lot: $line->lot,
                    package: $line->package,
                );
            }

            $line->refreshMove();
        });

        static::updating(function (MoveLine $line) {
            $changes = $line->getDirty();

            $relocations = array_intersect_key($changes, array_flip([
                'source_location_id',
                'destination_location_id',
                'lot_id',
                'package_id',
                'result_package_id',
                'uom_id',
            ]));

            if (array_key_exists('result_package_id', $relocations)) {
                $line->syncPackageLevel($relocations['result_package_id']);
            }

            $reservationAffected = (! empty($relocations) && ! array_key_exists('result_package_id', $relocations))
                || array_key_exists('qty', $changes);

            if ($reservationAffected && $line->product->is_storable && $line->state !== MoveState::DONE) {
                $line->rebookReservation($changes, $relocations);
            }
        });

        static::updated(function (MoveLine $line) {
            if ($line->wasChanged('qty') || $line->wasChanged('uom_id')) {
                $line->refreshMove();
            }
        });

        static::deleted(function (MoveLine $line) {
            if (
                ! float_is_zero($line->uom_qty, precisionRounding: $line->product->uom->rounding)
                && $line->move_id
                && ! $line->move->bypassesReservation()
            ) {
                ProductQuantity::applyReservationDelta(
                    product: $line->product,
                    location: $line->sourceLocation,
                    delta: -$line->uom_qty,
                    lot: $line->lot,
                    package: $line->package,
                );
            }

            $line->discardOrphanPackageLevel();

            $line->refreshMove();
        });
    }

    protected function refreshMove(): void
    {
        $this->move->computeQuantity();

        $this->move->computeState();

        $this->move->save();
    }

    protected function syncPackageLevel(mixed $resultPackageId): void
    {
        if (! $this->package_level_id) {
            return;
        }

        if (! empty($resultPackageId)) {
            $this->packageLevel->update(['package_id' => $resultPackageId]);

            return;
        }

        $packageLevel = $this->packageLevel;

        $this->package_level_id = null;

        if ($packageLevel->moveLines()->count() === 0) {
            $packageLevel->delete();
        }
    }

    protected function discardOrphanPackageLevel(): void
    {
        if (! $this->package_level_id) {
            return;
        }

        $packageLevel = $this->packageLevel;

        if (
            $packageLevel
            && $packageLevel->moveLines()->count() === 0
            && $packageLevel->moves()->count() === 0
        ) {
            $packageLevel->delete();
        }
    }

    protected function rebookReservation(array $changes, array $relocations): void
    {
        $reserveQty = $this->resolveReservedQuantity($changes, $relocations);

        $previousLocation = Location::find($this->getOriginal('source_location_id')) ?? $this->sourceLocation;

        $previousLot = Lot::find($this->getOriginal('lot_id'));

        $previousPackage = Package::find($this->getOriginal('package_id'));

        if (! float_is_zero($this->getOriginal('uom_qty'), precisionRounding: $this->product->uom->rounding)) {
            $this->applyQuantityChange(
                -$this->getOriginal('uom_qty'),
                $previousLocation,
                reserved: true,
                lot: $previousLot,
                package: $previousPackage,
            );
        }

        $location = array_key_exists('source_location_id', $relocations)
            ? Location::find($relocations['source_location_id'])
            : $previousLocation;

        if ($this->move->bypassesReservation($location)) {
            return;
        }

        $this->applyQuantityChange(
            $reserveQty,
            $location,
            reserved: true,
            lot: array_key_exists('lot_id', $relocations) ? Lot::find($relocations['lot_id']) : $previousLot,
            package: array_key_exists('package_id', $relocations) ? Package::find($relocations['package_id']) : $previousPackage,
        );
    }

    protected function resolveReservedQuantity(array $changes, array $relocations): float
    {
        if (! array_key_exists('qty', $changes) && ! array_key_exists('uom_id', $changes)) {
            return $this->uom_qty;
        }

        $uom = isset($relocations['uom_id']) ? UOM::find($relocations['uom_id']) : $this->uom;

        $quantity = $uom->computeQuantity(
            $changes['qty'] ?? $this->getOriginal('qty'),
            $this->product->uom,
            roundingMethod: 'HALF-UP'
        );

        if (float_compare($quantity, 0, precisionRounding: $this->product->uom->rounding) < 0) {
            throw new \Exception(__('inventories::system.move-line.negative-quantity-not-allowed'));
        }

        return $quantity;
    }

    protected function applyDefaults(): void
    {
        $this->operation_id ??= $this->move?->operation_id;

        $this->reference ??= $this->move?->reference;

        if ($this->uom) {
            $this->uom_qty = $this->uom->computeQuantity($this->qty, $this->product->uom, roundingMethod: 'HALF-UP');
        }

        $this->picking_description ??= $this->move?->description_picking;

        $this->product_id ??= $this->move?->product_id;

        $this->partner_id ??= $this->move?->partner_id;

        $this->uom_id ??= $this->product?->uom_id;

        $this->is_picked ??= $this->move?->is_picked ?? false;

        $this->source_location_id ??= $this->move?->source_location_id;

        $this->destination_location_id ??= $this->move?->destination_location_id;

        $this->scheduled_at ??= $this->move?->scheduled_at ?? now();
    }

    public function applyQuantityChange(
        float $quantity,
        Location $location,
        bool $reserved = false,
        $incomingDate = null,
        ?Lot $lot = null,
        ?Package $package = null,
    ): array {
        if (
            ! $this->product->is_storable
            || float_is_zero($quantity, precisionRounding: $this->uom->rounding)
        ) {
            return [0, false];
        }

        if ($reserved) {
            if (! $this->move->bypassesReservation($location)) {
                ProductQuantity::applyReservationDelta(
                    product: $this->product,
                    location: $location,
                    delta: $quantity,
                    lot: $lot,
                    package: $package
                );
            }

            return [0, $incomingDate];
        }

        [$availableQty, $incomingDate] = ProductQuantity::applyQuantityDelta(
            product: $this->product,
            location: $location,
            delta: $quantity,
            lot: $lot,
            package: $package,
            incomingDate: $incomingDate,
        );

        if ($availableQty < 0 && $lot) {
            $this->coverShortfallFromUntracked($location, $package, $quantity, $incomingDate, $lot, $availableQty);
        }

        return [$availableQty, $incomingDate];
    }

    protected function coverShortfallFromUntracked(
        Location $location,
        ?Package $package,
        float $quantity,
        $incomingDate,
        Lot $lot,
        float $availableQty
    ): void {
        $untracked = ProductQuantity::availableFor(
            product: $this->product,
            location: $location,
            lot: null,
            package: $package,
            strict: true
        );

        if (! $untracked) {
            return;
        }

        $transferable = min($untracked, abs($quantity));

        ProductQuantity::applyQuantityDelta(
            product: $this->product,
            location: $location,
            delta: -$transferable,
            lot: null,
            package: $package,
            incomingDate: $incomingDate,
        );

        ProductQuantity::applyQuantityDelta(
            product: $this->product,
            location: $location,
            delta: $transferable,
            lot: $lot,
            package: $package,
            incomingDate: $incomingDate,
        );
    }

    public function releaseCompetingReservations(
        Product $product,
        Location $location,
        float $quantity,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Collection $excludedLineIds = null
    ): void {
        $excludedLineIds = ($excludedLineIds ?? collect())->push($this->id);

        if ($this->move->bypassesReservation($location)) {
            return;
        }

        $candidates = MoveLine::query()
            ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
            ->where('product_id', $product->id)
            ->where('lot_id', $lot?->id)
            ->where('source_location_id', $location->id)
            ->where('package_id', $package?->id)
            ->where('uom_qty', '>', 0.0)
            ->where('is_picked', false)
            ->whereNotIn('id', $excludedLineIds->all())
            ->get()
            ->sortBy(fn (MoveLine $candidate) => $this->releasePriority($candidate));

        $movesToReserve = collect();

        $exhaustedIds = collect();

        $rounding = $this->uom->rounding;

        foreach ($candidates as $candidate) {
            $movesToReserve->push($candidate->move);

            if (float_compare($candidate->uom_qty, $quantity, precisionRounding: $rounding) > 0) {
                $candidate->update([
                    'qty' => $candidate->qty - $candidate->product->uom->computeQuantity($quantity, $candidate->uom, roundingMethod: 'HALF-UP'),
                ]);

                break;
            }

            $quantity -= $candidate->uom_qty;

            $exhaustedIds->push($candidate->id);

            if (float_is_zero($quantity, precisionRounding: $rounding)) {
                break;
            }
        }

        $exhausted = MoveLine::whereIn('id', $exhaustedIds)->get();

        $exhausted->pluck('move')
            ->merge($movesToReserve)
            ->unique('id')
            ->each(function (Move $move) {
                $move->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);

                $move->moveOrigins()->detach();
            });

        $exhausted->each(fn (MoveLine $line) => $line->delete());

        InventoryFacade::reserveMoves($movesToReserve->unique('id'));
    }

    protected function releasePriority(MoveLine $candidate): array
    {
        $scheduledAt = $candidate->operation_id
            ? ($candidate->move->operation->scheduled_at ?? $candidate->move->scheduled_at)
            : $candidate->move->scheduled_at;

        return [
            $candidate->move->operation_id !== $this->move->operation_id ? 1 : 0,
            $scheduledAt ? -$scheduledAt->timestamp : 0,
            -$candidate->id,
        ];
    }

    public function assignLotFromName(): void
    {
        if ($this->product->tracking === ProductTracking::LOT) {
            $existing = Lot::query()
                ->where('product_id', $this->product_id)
                ->whereRaw(db_dialect()->caseInsensitiveEquals('name'), [$this->lot_name])
                ->first();

            if ($existing) {
                $this->update(['lot_id' => $existing->id]);

                return;
            }
        }

        $this->update(['lot_id' => Lot::create($this->buildLotAttributes())->id]);
    }

    public function buildLotAttributes(): array
    {
        return [
            'name'       => $this->lot_name,
            'product_id' => $this->product_id,
            'company_id' => $this->company_id,
        ];
    }
}
