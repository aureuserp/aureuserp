<?php

namespace Webkul\Inventory\Models;

use Carbon\Carbon;
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
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class ProductQuantity extends Model
{
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

    protected array $context = [];

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

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

        static::creating(function ($productQuantity) {
            $productQuantity->creator_id ??= Auth::id();

            $productQuantity->incoming_at ??= now();
        });

        static::saving(function ($productQuantity) {
            $productQuantity->updateScheduledAt();
        });

        static::created(function ($productQuantity) {
            if ($productQuantity->package) {
                $productQuantity->package->update([
                    'pack_date'   => now(),
                ]);

                $productQuantity->computePackageLocationCompany();
            }

            if ($productQuantity->lot) {
                $productQuantity->lot->update([
                    'location_id' => $productQuantity->location_id,
                ]);
            }

            if (! $productQuantity->inventory_quantity_set) {
                $productQuantity->applyInventory();
            }
        });

        static::updated(function ($productQuantity) {
            if (! $productQuantity->inventory_quantity_set) {
                $productQuantity->applyInventory();
            }

            if ($productQuantity->wasChanged('location_id') || $productQuantity->wasChanged('company_id')) {
                if (! $productQuantity->package) {
                    return;
                }

                $productQuantity->computePackageLocationCompany();
            }

            static::deleteZeroQuantities();
        });
    }

    public function computePackageLocationCompany()
    {
        $package = $this->package;

        $package->location_id = null;

        $package->company_id  = null;

        $quantities = $package->quantities->filter(
            fn ($quantity) => float_compare($quantity->quantity, 0, precisionRounding: $quantity->uom->rounding) > 0
        );

        if ($quantities->isNotEmpty()) {
            $package->location_id = $quantities->first()->location_id;

            if ($package->quantities->every(fn ($quantity) => $quantity->company_id === $quantities->first()->company_id)) {
                $package->company_id = $quantities->first()->company_id;
            }
        }

        $package->save();
    }

    public function applyInventory()
    {
        if (float_compare($this->inventory_diff_quantity, 0.0, precisionRounding: $this->uom->rounding) == 0) {
            return;
        }

        $adjustmentLocation = Location::where('type', LocationType::INVENTORY)
            ->where('is_scrap', false)
            ->first();

        if (float_compare($this->inventory_diff_quantity, 0.0, precisionRounding: $this->uom->rounding) > 0) {
            $moveValues = $this->getInventoryMoveValues(
                $this->inventory_diff_quantity,
                $adjustmentLocation,
                $this->location,
                destinationPackage: $this->package
            );
        } else {
            $moveValues = $this->getInventoryMoveValues(
                -$this->inventory_diff_quantity,
                $this->location,
                $adjustmentLocation,
                package: $this->package
            );
        }

        $move = Move::create($moveValues);

        foreach ($moveValues['lines'] as $lineValues) {
            MoveLine::create(array_merge($lineValues, [
                'move_id' => $move->id,
            ]));
        }

        $this->product->context = [
            'location_id' => $this->location_id,
        ];

        ProductQuantity::updateOrCreate(
            [
                'location_id' => $adjustmentLocation->id,
                'product_id'  => $this->product_id,
                'lot_id'      => $this->lot_id,
            ], [
                'quantity'    => -$this->product->available_qty,
                'company_id'  => $this->company_id,
                'creator_id'  => Auth::id(),
                'incoming_at' => now(),
            ]
        );

        $this->updateQuietly([
            'inventory_diff_quantity' => 0.0,
            'inventory_quantity_set'  => false,
        ]);
    }

    public function getInventoryMoveValues(
        $qty,
        Location $sourceLocation,
        Location $destinationLocation,
        ?Package $package = null,
        ?Package $destinationPackage = null
    ) {
        if ($this->context['inventory_name'] ?? false) {
            $name = $this->context['inventory_name'];
        } elseif (float_is_zero($qty, precisionRounding: $this->product->uom->rounding)) {
            $name = 'Product Quantity Confirmed';
        } else {
            $name = 'Product Quantity Updated';
        }

        return [
            'name'                    => $name,
            'state'                   => MoveState::DONE,
            'quantity'                => $qty,
            'product_uom_qty'         => $qty,
            'is_picked'               => true,
            'product_id'              => $this->product_id,
            'uom_id'                  => $this->uom->id,
            'source_location_id'      => $sourceLocation->id,
            'destination_location_id' => $destinationLocation->id,
            'company_id'              => $this->company->id ?? Auth::user()->default_company_id,
            'lines'                   => [[
                'reference'               => $name,
                'qty'                     => $qty,
                'uom_qty'                 => $qty,
                'product_id'              => $this->product_id,
                'uom_id'                  => $this->uom->id,
                'source_location_id'      => $sourceLocation->id,
                'destination_location_id' => $destinationLocation->id,
                'lot_id'                  => $this->lot_id,
                'package_id'              => $package?->id,
                'result_package_id'       => $destinationPackage?->id,
                'company_id'              => $this->company->id ?? Auth::user()->default_company_id,
            ]],
        ];
    }

    public static function updateReservedQuantity(
        Product $product,
        Location $location,
        float $quantity,
        ?Lot $lot = null,
        ?Package $package = null
    ): void {
        static::updateAvailableQuantity(
            product: $product,
            location: $location,
            reservedQuantity: $quantity,
            lot: $lot,
            package: $package,
        );
    }

    public static function updateAvailableQuantity(
        Product $product,
        Location $location,
        mixed $quantity = false,
        mixed $reservedQuantity = false,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Carbon $incomingDate = null,
    ): array {
        if (! $quantity && ! $reservedQuantity) {
            throw new \Exception(__('inventories::system.product-quantity.quantity-not-set'));
        }

        $quants = static::gather($product, $location, lot: $lot, package: $package, strict: true);

        if ($lot && $quantity > 0) {
            $quants = $quants->filter(fn ($q) => $q->lot_id);
        }

        if ($location->shouldBypassReservation()) {
            $incomingDates = [];
        } else {
            $incomingDates = $quants
                ->filter(fn ($q) => $q->incoming_date && float_compare($q->quantity, 0, precisionRounding: $q->product->uom->rounding) > 0)
                ->pluck('incoming_date')
                ->map(fn ($date) => Carbon::parse($date))
                ->all();
        }

        if ($incomingDate) {
            $incomingDates[] = $incomingDate;
        }

        $incomingDate = ! empty($incomingDates) ? min($incomingDates) : now();

        $quant = null;

        if ($quants->isNotEmpty()) {
            $quant = self::whereIn('id', $quants->pluck('id'))
                ->orderBy('lot_id')
                ->lockForUpdate()
                ->first();
        }

        if ($quant) {
            $vals = ['incoming_date' => $incomingDate];

            if ($quantity) {
                $vals['quantity'] = $quant->quantity + $quantity;
            }

            if ($reservedQuantity) {
                $vals['reserved_quantity'] = $quant->reserved_quantity + $reservedQuantity;
            }

            $quant->update($vals);
        } else {
            $vals = [
                'product_id'    => $product->id,
                'location_id'   => $location->id,
                'lot_id'        => $lot?->id,
                'package_id'    => $package?->id,
                'incoming_date' => $incomingDate,
            ];

            if ($quantity) {
                $vals['quantity'] = $quantity;
            }

            if ($reservedQuantity) {
                $vals['reserved_quantity'] = $reservedQuantity;
            }

            self::create($vals);
        }

        return [
            static::getAvailableQuantity($product, $location, lot: $lot, package: $package, strict: true, allowNegative: true),
            $incomingDate,
        ];
    }

    public static function deleteZeroQuantities(): void
    {
        static::where(function ($query) {
            $query->whereRaw('ROUND(quantity, ?) = 0', [6])
                ->orWhereNull('quantity');
        })
            ->whereRaw('ROUND(reserved_quantity, ?) = 0', [6])
            ->whereNull('user_id')
            ->delete();
    }

    public function updateScheduledAt()
    {
        $this->scheduled_at = Carbon::create(
            now()->year,
            settings(OperationSettings::class)->annual_inventory_month,
            settings(OperationSettings::class)->annual_inventory_day,
            0,
            0,
            0
        );

        if ($this->location?->cyclic_inventory_frequency) {
            $this->scheduled_at = now()->addDays($this->location->cyclic_inventory_frequency);
        }
    }

    public static function gather(
        Product $product,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
        float $qty = 0,
    ): Collection {
        $removalStrategy = static::getRemovalStrategy($product, $location);

        $domain = static::getGatherDomain($product, $location, $lot, $package, $partner, $strict);

        $order = static::getRemovalStrategyOrder($removalStrategy);

        $query = static::query()->where($domain);

        if ($order) {
            $query->orderByRaw($order);
        }

        $quants = $query->get();

        return $quants->sortBy(fn ($q) => $q->lot_id ? 0 : 1)->values();
    }

    public static function getRemovalStrategy(Product $product, Location $location): string
    {
        if ($product->category?->removal_strategy) {
            return $product->category->removal_strategy;
        }

        $loc = $location;

        while ($loc) {
            if ($loc->removal_strategy) {
                return $loc->removal_strategy;
            }

            $loc = $loc->parent;
        }

        return 'fifo';
    }

    public static function getRemovalStrategyOrder(string $removalStrategy): ?string
    {
        return match ($removalStrategy) {
            'fifo'    => 'incoming_at ASC, id',
            'lifo'    => 'incoming_at DESC, id DESC',
            'closest' => null,
            default   => throw new \RuntimeException(__('inventories::system.product-quantity.removal-strategy-not-implemented', ['strategy' => $removalStrategy])),
        };
    }

    public static function getGatherDomain(
        Product $product,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
    ): \Closure {
        return function ($query) use ($product, $location, $lot, $package, $partner, $strict) {
            $query->where('product_id', $product->id);

            if (! $strict) {
                if ($lot) {
                    $query->where(fn ($q) => $q->where('lot_id', $lot->id)->orWhereNull('lot_id'));
                }

                if ($package) {
                    $query->where('package_id', $package->id);
                }

                if ($partner) {
                    $query->where('partner_id', $partner->id);
                }

                $childIds = Location::where('parent_path', 'LIKE', $location->parent_path.'%')->pluck('id');

                $query->whereIn('location_id', $childIds);
            } else {
                if ($lot) {
                    $query->where(fn ($q) => $q->where('lot_id', $lot->id)->orWhereNull('lot_id'));
                } else {
                    $query->whereNull('lot_id');
                }

                $query->where('package_id', $package?->id);
                $query->where('partner_id', $partner?->id);
                $query->where('location_id', $location->id);
            }
        };
    }

    public static function getAvailableQuantity(
        Product $product,
        Location $location,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
        bool $allowNegative = false,
    ): float {
        $quants = static::gather($product, $location, $lot, $package, $partner, $strict);

        $rounding = $product->uom->rounding;

        if (! in_array($product->tracking, [ProductTracking::LOT, ProductTracking::SERIAL])) {
            $available = $quants->sum('quantity') - $quants->sum('reserved_quantity');

            if ($allowNegative) {
                return $available;
            }

            return float_compare($available, 0.0, precisionRounding: $rounding) >= 0 ? $available : 0.0;
        }

        $availableQuantities = array_fill_keys(
            array_merge($quants->pluck('lot_id')->filter()->unique()->toArray(), ['untracked']),
            0.0
        );

        foreach ($quants as $quant) {
            if (! $quant->lot_id && $strict && $lot) {
                continue;
            }

            $bucketKey = $quant->lot_id ?? 'untracked';

            $availableQuantities[$bucketKey] = ($availableQuantities[$bucketKey] ?? 0.0) + ($quant->quantity - $quant->reserved_quantity);
        }

        if ($allowNegative) {
            return (float) array_sum($availableQuantities);
        }

        return (float) array_sum(array_filter($availableQuantities, fn ($v) => float_compare($v, 0.0, precisionRounding: $rounding) > 0));
    }

    public static function getReserveQuantity(
        Product $product,
        Location $location,
        float $quantity,
        ?Packaging $productPackaging = null,
        ?UOM $uom = null,
        ?Lot $lot = null,
        ?Package $package = null,
        ?Partner $partner = null,
        bool $strict = false,
    ): array {
        $rounding = $product->uom->rounding;

        $quants = static::gather($product, $location, $lot, $package, $partner, $strict);

        $availableQuantity = static::getAvailableQuantity($product, $location, $lot, $package, $partner, $strict);

        $quantity = min($quantity, $availableQuantity);

        if (! $strict && $uom && $product->uom->id !== $uom->id) {
            $quantityMoveUom = $product->uom->computeQuantity($quantity, $uom, roundingMethod: 'DOWN');

            $quantity = $uom->computeQuantity($quantityMoveUom, $product->uom, roundingMethod: 'HALF-UP');
        }

        if ($product->tracking === ProductTracking::SERIAL) {
            if (float_compare($quantity, (float) (int) $quantity, precisionRounding: $rounding) !== 0) {
                $quantity = 0.0;
            }
        }

        $reservedQuants = [];

        if (float_compare($quantity, 0.0, precisionRounding: $rounding) === 0) {
            return $reservedQuants;
        }

        if (float_compare($quantity, 0.0, precisionRounding: $rounding) > 0) {
            $available = $quants->filter(fn ($q) => float_compare($q->quantity, 0.0, precisionRounding: $rounding) > 0)->sum('quantity')
                - $quants->sum('reserved_quantity');
        } else {
            $available = $quants->sum('reserved_quantity');

            if (float_compare(abs($quantity), $available, precisionRounding: $rounding) > 0) {
                throw new \RuntimeException(__('inventories::system.product-quantity.unreserve-more-than-stock', ['name' => $product->name]));
            }
        }

        $negativeReserved = [];

        foreach ($quants as $quant) {
            $net = $quant->quantity - $quant->reserved_quantity;

            if (float_compare($net, 0.0, precisionRounding: $rounding) < 0) {
                $negKey = implode('_', [$quant->location_id, $quant->lot_id, $quant->package_id, $quant->partner_id]);

                $negativeReserved[$negKey] = ($negativeReserved[$negKey] ?? 0.0) + $net;
            }
        }

        foreach ($quants as $quant) {
            if (float_compare($quantity, 0.0, precisionRounding: $rounding) > 0) {
                $maxOnQuant = $quant->quantity - $quant->reserved_quantity;

                if (float_compare($maxOnQuant, 0.0, precisionRounding: $rounding) <= 0) {
                    continue;
                }

                $negKey = implode('_', [$quant->location_id, $quant->lot_id, $quant->package_id, $quant->partner_id]);

                $negQty = $negativeReserved[$negKey] ?? 0.0;

                if ($negQty) {
                    $toRemove = min(abs($negQty), $maxOnQuant);

                    $negativeReserved[$negKey] += $toRemove;

                    $maxOnQuant -= $toRemove;
                }

                if (float_compare($maxOnQuant, 0.0, precisionRounding: $rounding) <= 0) {
                    continue;
                }

                $toReserve = min($maxOnQuant, $quantity);

                $reservedQuants[] = [$quant, $toReserve];

                $quantity -= $toReserve;

                $available -= $toReserve;
            } else {
                $toRelease = min($quant->reserved_quantity, abs($quantity));

                $reservedQuants[] = [$quant, -$toRelease];

                $quantity += $toRelease;

                $available += $toRelease;
            }

            if (
                float_is_zero($quantity, precisionRounding: $rounding)
                || float_is_zero($available, precisionRounding: $rounding)
            ) {
                break;
            }
        }

        return $reservedQuants;
    }

    public static function getQuantitiesByProductsLocations(mixed $productIds, mixed $locationIds, array $extraFilters = []): array
    {
        $result = [];

        if ($productIds->isEmpty() || $locationIds->isEmpty()) {
            return $result;
        }

        $filters = [
            ['product_id', 'in', $productIds->all()],
            ['location_id', 'child_of', $locationIds->all()],
        ];

        if (! empty($extraFilters)) {
            $filters = array_merge($filters, $extraFilters);
        }

        $neededQuants = static::query()
            ->where(function ($query) use ($filters) {
                foreach ($filters as $condition) {
                    [$column, $operator, $value] = count($condition) === 2
                        ? [$condition[0], '=', $condition[1]]
                        : $condition;

                    if ($operator === 'in') {
                        $query->whereIn($column, $value);

                        continue;
                    }

                    if ($operator === 'child_of') {
                        $locationParentPaths = Location::query()
                            ->whereIn('id', $value)
                            ->pluck('parent_path')
                            ->filter();

                        $locationIds = $locationParentPaths->isEmpty()
                            ? collect()
                            : Location::query()
                                ->where(function ($locationQuery) use ($locationParentPaths) {
                                    foreach ($locationParentPaths as $parentPath) {
                                        $locationQuery->orWhere('parent_path', 'like', $parentPath.'%');
                                    }
                                })
                                ->pluck('id');

                        $query->whereIn($column, $locationIds);

                        continue;
                    }

                    $query->where($column, $operator, $value);
                }
            })
            ->orderBy('lot_id')
            ->get()
            ->groupBy(fn ($quant) => implode('_', [
                $quant->product_id,
                $quant->location_id,
                $quant->lot_id,
                $quant->package_id,
            ]));

        foreach ($neededQuants as $key => $quants) {
            $result[$key] = $quants;
        }

        return $result;
    }

    protected static function newFactory(): ProductQuantityFactory
    {
        return ProductQuantityFactory::new();
    }
}
