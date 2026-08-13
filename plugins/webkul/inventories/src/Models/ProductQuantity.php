<?php

namespace Webkul\Inventory\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\ProductQuantityFactory;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Support\PlannedReservation;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Enums\ProductRemoval;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;
use Webkul\Support\Traits\BelongsToCompany;

class ProductQuantity extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'inventories_product_quantities';

    protected $fillable = [
        'quantity',
        'reserved_quantity',
        'counted_quantity',
        'difference_quantity',
        'inventory_diff_quantity',
        'inventory_quantity_set',
        'scheduled_at',
        'incoming_at',
        'product_id',
        'location_id',
        'storage_category_id',
        'lot_id',
        'package_id',
        'partner_id',
        'user_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'inventory_quantity_set' => 'boolean',
        'scheduled_at'           => 'date',
        'incoming_at'            => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function storageCategory(): BelongsTo
    {
        return $this->belongsTo(StorageCategory::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUomAttribute(): UOM
    {
        return $this->product->uom;
    }

    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity - $this->reserved_quantity;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (ProductQuantity $stock) {
            $stock->creator_id ??= Auth::id();

            $stock->incoming_at ??= now();
        });

        static::saving(function (ProductQuantity $stock) {
            $stock->refreshCountSchedule();

            $stock->company_id = $stock->location?->company_id ?? $stock->company_id;
        });

        static::created(function (ProductQuantity $stock) {
            if ($stock->package) {
                $stock->package->update(['pack_date' => now()]);

                $stock->syncPackagePlacement();
            }

            $stock->lot?->update(['location_id' => $stock->location_id]);

            if (! $stock->inventory_quantity_set) {
                $stock->applyCountedDifference();
            }
        });

        static::updated(function (ProductQuantity $stock) {
            if (! $stock->inventory_quantity_set) {
                $stock->applyCountedDifference();
            }

            if ($stock->wasChanged('location_id') || $stock->wasChanged('company_id')) {
                if (! $stock->package) {
                    return;
                }

                $stock->syncPackagePlacement();
            }

            static::purgeEmpty();
        });
    }

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    public function syncPackagePlacement(): void
    {
        $package = $this->package;

        $stocked = $package->quantities->filter(
            fn (self $stock) => float_compare($stock->quantity, 0, precisionRounding: $stock->uom->rounding) > 0
        );

        $package->location_id = $stocked->first()?->location_id;

        $package->company_id = $stocked->isNotEmpty()
            && $package->quantities->every(fn (self $stock) => $stock->company_id === $stocked->first()->company_id)
                ? $stocked->first()->company_id
                : null;

        $package->save();
    }

    public function applyCountedDifference(): void
    {
        $difference = $this->inventory_diff_quantity;

        if (float_is_zero($difference, precisionRounding: $this->uom->rounding)) {
            return;
        }

        $adjustmentLocation = Location::query()
            ->where('type', LocationType::INVENTORY)
            ->where('is_scrap', false)
            ->where('company_id', $this->location->company_id)
            ->first();

        $isIncrease = float_compare($difference, 0.0, precisionRounding: $this->uom->rounding) > 0;

        $this->recordAdjustmentMove(
            abs($difference),
            $isIncrease ? $adjustmentLocation : $this->location,
            $isIncrease ? $this->location : $adjustmentLocation,
            $isIncrease ? null : $this->package,
            $isIncrease ? $this->package : null,
        );

        static::updateOrCreate([
            'location_id' => $adjustmentLocation->id,
            'product_id'  => $this->product_id,
            'lot_id'      => $this->lot_id,
        ], [
            'quantity'    => -$this->product->available_qty,
            'company_id'  => $this->company_id,
            'creator_id'  => Auth::id(),
            'incoming_at' => now(),
        ]);

        $this->updateQuietly([
            'inventory_diff_quantity' => 0.0,
            'inventory_quantity_set'  => false,
        ]);
    }

    protected function recordAdjustmentMove(
        float $quantity,
        Location $source,
        Location $destination,
        ?Package $package,
        ?Package $destinationPackage
    ): Move {
        $reference = float_is_zero($quantity, precisionRounding: $this->product->uom->rounding)
            ? 'Product Quantity Confirmed'
            : 'Product Quantity Updated';

        $companyId = $this->company->id ?? current_company_id();

        $move = Move::create([
            'name'                    => $reference,
            'state'                   => MoveState::DONE,
            'quantity'                => $quantity,
            'product_uom_qty'         => $quantity,
            'is_picked'               => true,
            'is_inventory'            => true,
            'product_id'              => $this->product_id,
            'uom_id'                  => $this->uom->id,
            'source_location_id'      => $source->id,
            'destination_location_id' => $destination->id,
            'company_id'              => $companyId,
        ]);

        $move->lines()->create([
            'reference'               => $reference,
            'qty'                     => $quantity,
            'uom_qty'                 => $quantity,
            'product_id'              => $this->product_id,
            'uom_id'                  => $this->uom->id,
            'source_location_id'      => $source->id,
            'destination_location_id' => $destination->id,
            'lot_id'                  => $this->lot_id,
            'package_id'              => $package?->id,
            'result_package_id'       => $destinationPackage?->id,
            'company_id'              => $companyId,
        ]);

        return $move;
    }

    public static function applyReservationDelta(
        Product $product,
        Location $location,
        float $delta,
        ?Lot $lot = null,
        ?Package $package = null
    ): void {
        static::adjustStock($product, $location, reservedDelta: $delta, lot: $lot, package: $package);
    }

    public static function applyQuantityDelta(
        Product $product,
        Location $location,
        float $delta,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Carbon $incomingDate = null,
    ): array {
        return static::adjustStock($product, $location, quantityDelta: $delta, lot: $lot, package: $package, incomingDate: $incomingDate);
    }

    protected static function adjustStock(
        Product $product,
        Location $location,
        ?float $quantityDelta = null,
        ?float $reservedDelta = null,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Carbon $incomingDate = null,
    ): array {
        if (! $quantityDelta && ! $reservedDelta) {
            throw new \Exception(__('inventories::system.product-quantity.quantity-not-set'));
        }

        $candidates = static::findFor($product, $location, lot: $lot, package: $package, strict: true);

        if ($lot && $quantityDelta > 0) {
            $candidates = $candidates->filter(fn (self $stock) => $stock->lot_id);
        }

        $incomingDate ??= now();

        $stock = $candidates->isEmpty()
            ? null
            : static::query()
                ->whereIn('id', $candidates->pluck('id'))
                ->orderBy('lot_id')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

        if ($stock) {
            $stock->update(array_filter([
                'quantity'          => $quantityDelta ? $stock->quantity + $quantityDelta : null,
                'reserved_quantity' => $reservedDelta ? $stock->reserved_quantity + $reservedDelta : null,
            ], fn ($value) => $value !== null));
        } else {
            static::create(array_filter([
                'product_id'        => $product->id,
                'location_id'       => $location->id,
                'lot_id'            => $lot?->id,
                'package_id'        => $package?->id,
                'quantity'          => $quantityDelta ?: null,
                'reserved_quantity' => $reservedDelta ?: null,
            ], fn ($value) => $value !== null));
        }

        return [
            static::availableFor($product, $location, lot: $lot, package: $package, strict: true, allowNegative: true),
            $incomingDate,
        ];
    }

    public static function purgeEmpty(): void
    {
        static::query()
            ->where(fn ($query) => $query->whereRaw('ROUND(quantity, ?) = 0', [6])->orWhereNull('quantity'))
            ->whereRaw('ROUND(reserved_quantity, ?) = 0', [6])
            ->whereNull('user_id')
            ->delete();
    }

    public function refreshCountSchedule(): void
    {
        if ($this->location?->cyclic_inventory_frequency) {
            $this->scheduled_at = now()->addDays($this->location->cyclic_inventory_frequency);

            return;
        }

        $settings = settings(OperationSettings::class);

        $this->scheduled_at = Carbon::create(
            now()->year,
            $settings->annual_inventory_month,
            $settings->annual_inventory_day,
            0,
            0,
            0
        );
    }

    public function scopeMatching(
        Builder $query,
        Product $product,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
    ): Builder {
        $query->where('product_id', $product->id);

        if ($lot) {
            $query->where(fn ($nested) => $nested->where('lot_id', $lot->id)->orWhereNull('lot_id'));
        } elseif ($strict) {
            $query->whereNull('lot_id');
        }

        if ($strict) {
            return $query
                ->where('package_id', $package?->id)
                ->where('partner_id', $partner?->id)
                ->where('location_id', $location->id);
        }

        if ($package) {
            $query->where('package_id', $package->id);
        }

        if ($partner) {
            $query->where('partner_id', $partner->id);
        }

        return $query->whereIn('location_id', Location::query()->descendantsOf($location)->select('id'));
    }

    public static function findFor(
        Product $product,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
    ): Collection {
        $query = static::query()->matching($product, $location, $lot, $package, $partner, $strict);

        if ($ordering = static::removalOrdering(static::resolveRemovalStrategy($product, $location))) {
            $query->orderByRaw($ordering);
        }

        return $query->get()
            ->sortBy(fn (self $stock) => $stock->lot_id ? 0 : 1)
            ->values();
    }

    public static function resolveRemovalStrategy(Product $product, Location $location): string
    {
        if ($strategy = $product->category?->removal_strategy) {
            return static::normalizeRemovalStrategy($strategy);
        }

        for ($current = $location; $current; $current = $current->parent) {
            if ($current->removal_strategy) {
                return static::normalizeRemovalStrategy($current->removal_strategy);
            }
        }

        return ProductRemoval::FIFO->value;
    }

    protected static function normalizeRemovalStrategy(ProductRemoval|string $strategy): string
    {
        return $strategy instanceof ProductRemoval ? $strategy->value : $strategy;
    }

    public static function removalOrdering(string $strategy): ?string
    {
        return match ($strategy) {
            'fifo'    => 'incoming_at ASC, id',
            'lifo'    => 'incoming_at DESC, id DESC',
            'closest' => null,
            default   => throw new \RuntimeException(__('inventories::system.product-quantity.removal-strategy-not-implemented', ['strategy' => $strategy])),
        };
    }

    public static function availableFor(
        Product $product,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
        bool $allowNegative = false,
    ): float {
        $stocks = static::findFor($product, $location, $lot, $package, $partner, $strict);

        $rounding = $product->uom->rounding;

        if (! in_array($product->tracking, [ProductTracking::LOT, ProductTracking::SERIAL])) {
            $available = $stocks->sum('quantity') - $stocks->sum('reserved_quantity');

            if ($allowNegative) {
                return $available;
            }

            return float_compare($available, 0.0, precisionRounding: $rounding) >= 0 ? $available : 0.0;
        }

        $perLot = [];

        foreach ($stocks as $stock) {
            if (! $stock->lot_id && $strict && $lot) {
                continue;
            }

            $bucket = $stock->lot_id ?? 'untracked';

            $perLot[$bucket] = ($perLot[$bucket] ?? 0.0) + ($stock->quantity - $stock->reserved_quantity);
        }

        if ($allowNegative) {
            return (float) array_sum($perLot);
        }

        return (float) array_sum(
            array_filter($perLot, fn ($value) => float_compare($value, 0.0, precisionRounding: $rounding) > 0)
        );
    }

    public static function planReservation(
        Product $product,
        Location $location,
        float $quantity,
        ?UOM $uom = null,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
    ): array {
        $rounding = $product->uom->rounding;

        $stocks = static::findFor($product, $location, $lot, $package, $partner, $strict);

        $quantity = min($quantity, static::availableFor($product, $location, $lot, $package, $partner, $strict));

        if (! $strict && $uom && $product->uom->id !== $uom->id) {
            $inMoveUom = $product->uom->computeQuantity($quantity, $uom, roundingMethod: 'DOWN');

            $quantity = $uom->computeQuantity($inMoveUom, $product->uom, roundingMethod: 'HALF-UP');
        }

        if (
            $product->tracking === ProductTracking::SERIAL
            && float_compare($quantity, (float) (int) $quantity, precisionRounding: $rounding) !== 0
        ) {
            $quantity = 0.0;
        }

        if (float_is_zero($quantity, precisionRounding: $rounding)) {
            return [];
        }

        $isReserving = float_compare($quantity, 0.0, precisionRounding: $rounding) > 0;

        if ($isReserving) {
            $available = $stocks->filter(fn (self $stock) => float_compare($stock->quantity, 0.0, precisionRounding: $rounding) > 0)->sum('quantity')
                - $stocks->sum('reserved_quantity');
        } else {
            $available = $stocks->sum('reserved_quantity');

            if (float_compare(abs($quantity), $available, precisionRounding: $rounding) > 0) {
                throw new \RuntimeException(__('inventories::system.product-quantity.unreserve-more-than-stock', ['name' => $product->name]));
            }
        }

        $shortfalls = static::shortfallsPerBucket($stocks, $rounding);

        $plan = [];

        foreach ($stocks as $stock) {
            if ($isReserving) {
                $spare = $stock->quantity - $stock->reserved_quantity;

                if (float_compare($spare, 0.0, precisionRounding: $rounding) <= 0) {
                    continue;
                }

                $bucket = static::bucketKey($stock);

                if ($shortfall = $shortfalls[$bucket] ?? 0.0) {
                    $offset = min(abs($shortfall), $spare);

                    $shortfalls[$bucket] += $offset;

                    $spare -= $offset;
                }

                if (float_compare($spare, 0.0, precisionRounding: $rounding) <= 0) {
                    continue;
                }

                $taken = min($spare, $quantity);

                $plan[] = new PlannedReservation($stock, $taken);

                $quantity -= $taken;

                $available -= $taken;
            } else {
                $released = min($stock->reserved_quantity, abs($quantity));

                $plan[] = new PlannedReservation($stock, -$released);

                $quantity += $released;

                $available += $released;
            }

            if (
                float_is_zero($quantity, precisionRounding: $rounding)
                || float_is_zero($available, precisionRounding: $rounding)
            ) {
                break;
            }
        }

        return $plan;
    }

    protected static function shortfallsPerBucket(Collection $stocks, float $rounding): array
    {
        $shortfalls = [];

        foreach ($stocks as $stock) {
            $spare = $stock->quantity - $stock->reserved_quantity;

            if (float_compare($spare, 0.0, precisionRounding: $rounding) < 0) {
                $bucket = static::bucketKey($stock);

                $shortfalls[$bucket] = ($shortfalls[$bucket] ?? 0.0) + $spare;
            }
        }

        return $shortfalls;
    }

    protected static function bucketKey(self $stock): string
    {
        return implode('_', [$stock->location_id, $stock->lot_id, $stock->package_id, $stock->partner_id]);
    }

    protected static function newFactory(): ProductQuantityFactory
    {
        return ProductQuantityFactory::new();
    }
}
