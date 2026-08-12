<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Database\Factories\LocationFactory;
use Webkul\Inventory\Enums\AllowNewProduct;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\SubLocation;
use Webkul\Product\Enums\ProductRemoval;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Location extends Model
{
    use BelongsToCompany;
    use HasCustomFields, HasFactory, SoftDeletes;

    protected $table = 'inventories_locations';

    protected $fillable = [
        'position_x',
        'position_y',
        'position_z',
        'type',
        'name',
        'full_name',
        'description',
        'parent_path',
        'barcode',
        'removal_strategy',
        'cyclic_inventory_frequency',
        'last_inventory_date',
        'next_inventory_date',
        'is_scrap',
        'is_replenish',
        'is_dock',
        'parent_id',
        'storage_category_id',
        'warehouse_id',
        'company_id',
        'creator_id',
        'deleted_at',
    ];

    protected $casts = [
        'type'                => LocationType::class,
        'removal_strategy'    => ProductRemoval::class,
        'last_inventory_date' => 'date',
        'next_inventory_date' => 'date',
        'is_scrap'            => 'boolean',
        'is_replenish'        => 'boolean',
        'is_dock'             => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function putawayRules(): HasMany
    {
        return $this->hasMany(PutawayRule::class, 'in_location_id');
    }

    public function quantities(): HasMany
    {
        return $this->hasMany(ProductQuantity::class, 'location_id');
    }

    public function storageCategory(): BelongsTo
    {
        return $this->belongsTo(StorageCategory::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsStockLocationAttribute(): bool
    {
        if (! $this->warehouse_id) {
            return false;
        }

        return $this->warehouse->lot_stock_location_id == $this->id
            || ($this->parent_id && $this->parent->is_stock_location);
    }

    public function scopeDescendantsOf(Builder $query, self $location): Builder
    {
        return $query->where('parent_path', 'like', $location->parent_path.'%');
    }

    public function scopeInternal(Builder $query): Builder
    {
        return $query->where('type', LocationType::INTERNAL);
    }

    public function internalDescendants(): Collection
    {
        return static::query()
            ->internal()
            ->descendantsOf($this)
            ->get()
            ->toBase();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Location $location) {
            $location->creator_id ??= Auth::id();

            $location->warehouse_id = $location->parent_id
                ? Location::find($location->parent_id)?->warehouse_id
                : null;

            $location->refreshNextInventoryDate();
        });

        static::created(function (Location $location) {
            $location->updateParentPath();

            $location->updateFullName();

            $location->saveQuietly();
        });

        static::saving(function (Location $location) {
            $location->refreshNextInventoryDate();

            $location->updateParentPath();

            $location->updateFullName();
        });

        static::updating(function (Location $location) {
            if ($location->isDirty('is_replenish') && $location->is_replenish) {
                $exists = static::query()
                    ->where('id', '!=', $location->id)
                    ->where('is_replenish', true)
                    ->where(fn ($q) => $q
                        ->whereIn('id', $location->ancestorIds())
                        ->orWhereIn('parent_id', [$location->id]))
                    ->first();
                if ($exists) {
                    throw new \Exception("Another parent/sub replenish location {$exists->name} exists, if you wish to change it, uncheck it first");
                }
            }

            if ($location->isDirty('is_scrap') && $location->is_scrap) {
                $usedByMrp = OperationType::where('type', OperationTypeEnum::MANUFACTURE)
                    ->where('destination_location_id', $location->id)->exists();

                if ($usedByMrp) {
                    throw new \Exception('You cannot set a location as a scrap location when it is assigned as a destination location for a manufacturing type operation.');
                }
            }

            if ($location->isDirty('company_id')) {
                throw new \Exception('Changing the company of this record is forbidden at this point, you should rather archive it and create a new one.');
            }

            if (
                $location->isDirty('type')
                && $location->type === LocationType::VIEW
                && $location->quantities()->where('quantity', '!=', 0)->exists()
            ) {
                throw new \Exception("This location's type can not be changed to view as it contains products.");
            }

            if (
                (
                    $location->isDirty('type')
                    || $location->isDirty('is_scrap')
                )
                && $location->quantities()->where(fn ($q) => $q->where('quantity', '!=', 0)->orWhere('reserved_quantity', '!=', 0))->exists()
            ) {
                throw new \Exception("Internal locations having stock can't be converted");
            }
        });

        static::updated(function (Location $location) {
            $location->updateChildrenParentPaths();

            if ($location->wasChanged('full_name')) {
                $location->updateChildrenFullNames();
            }
        });

        static::deleting(function (Location $location) {
            $warehouse = Warehouse::where(function ($q) use ($location) {
                $q->where('lot_stock_location_id', $location->id)
                    ->orWhere('view_location_id', $location->id);
            })
                ->first();

            if ($warehouse) {
                throw new \Exception(__('You cannot archive location :location because it is used by warehouse :warehouse', [
                    'location'  => $location->name,
                    'warehouse' => $warehouse->name,
                ]));
            }

            $descendants = $location->trashedAwareDescendants();

            $stockedLocationIds = $descendants
                ->filter(fn (Location $descendant) => $descendant->type === LocationType::INTERNAL)
                ->pluck('id')
                ->push($location->id)
                ->all();

            $remainingStock = ProductQuantity::query()
                ->where(fn ($query) => $query->where('quantity', '!=', 0)->orWhere('reserved_quantity', '!=', 0))
                ->whereIn('location_id', $stockedLocationIds)
                ->get();

            if ($remainingStock->isNotEmpty()) {
                throw new \Exception(__("You can't disable locations :locations because they still contain products.", [
                    'locations' => $remainingStock->pluck('location.name')->unique()->implode(', '),
                ]));
            }

            $descendants->each(fn (Location $descendant) => $descendant->delete());
        });

        static::forceDeleting(function (Location $location) {
            $location->trashedAwareDescendants()->each(fn (Location $descendant) => $descendant->forceDelete());
        });

        static::restored(function (Location $location) {
            $location->trashedAwareDescendants()->each(fn (Location $descendant) => $descendant->restore());
        });
    }

    protected function trashedAwareDescendants(): Collection
    {
        return static::withTrashed()
            ->descendantsOf($this)
            ->whereKeyNot($this->id)
            ->get()
            ->toBase();
    }

    protected function refreshNextInventoryDate(): void
    {
        $this->next_inventory_date = empty($this->cyclic_inventory_frequency)
            ? null
            : now()->addDays((int) $this->cyclic_inventory_frequency);
    }

    public function resolvePutawayLocation(
        ?Product $product,
        float $quantity = 0,
        ?Package $package = null,
        ?Packaging $packaging = null,
        ?array $reservedPerLocation = null,
        array $excludeMoveLineIds = [],
        ?Collection $products = null
    ): Location {
        $products = collect([$product])
            ->merge($products ?? collect())
            ->filter()
            ->unique('id')
            ->values();

        $candidates = $this->internalDescendants();

        $rules = $this->applicablePutawayRules($products, $package?->packageType ?? $packaging?->packageType);

        $target = $rules->isEmpty()
            ? null
            : $this->firstAvailablePutawayLocation(
                $rules,
                $product,
                $quantity,
                $package,
                $packaging,
                $this->occupancyPerLocation($candidates, $product, $package, $reservedPerLocation, $excludeMoveLineIds),
            );

        if ($target) {
            return $target;
        }

        return $candidates->isNotEmpty() && $this->type === LocationType::VIEW
            ? $candidates->first()
            : $this;
    }

    protected function applicablePutawayRules(Collection $products, ?PackageType $packageType): Collection
    {
        $productIds = $products->pluck('id');

        $categoryIds = $this->categoryAncestry($products);

        return $this->putawayRules
            ->filter(fn ($rule) => (! $rule->product_id || $productIds->contains($rule->product_id))
                && (! $rule->category_id || $categoryIds->contains($rule->category_id))
                && ($rule->packageTypes->isEmpty() || ($packageType && $rule->packageTypes->contains('id', $packageType->id)))
            )
            ->sortByDesc(fn ($rule) => [
                $rule->packageTypes->isNotEmpty() ? 1 : 0,
                $rule->product_id ? 1 : 0,
                $rule->category_id === $categoryIds->first() ? 1 : 0,
                $rule->category_id ? 1 : 0,
            ])
            ->values();
    }

    protected function categoryAncestry(Collection $products): Collection
    {
        $categoryIds = $products->pluck('category_id')->filter()->unique();

        if ($categoryIds->count() !== 1) {
            return collect();
        }

        $ancestry = collect();

        $category = Category::find($categoryIds->first());

        while ($category) {
            $ancestry->push($category->id);

            $category = $category->parent_id ? $category->parent : null;
        }

        return $ancestry;
    }

    protected function occupancyPerLocation(
        Collection $candidates,
        ?Product $product,
        ?Package $package,
        ?array $reservedPerLocation,
        array $excludeMoveLineIds
    ): array {
        $occupancy = [];

        if ($candidates->pluck('storage_category_id')->filter()->isNotEmpty()) {
            $occupancy = $package?->package_type_id
                ? $this->packageOccupancy($candidates, $package->package_type_id, $excludeMoveLineIds)
                : ($product ? $this->productOccupancy($candidates, $product, $excludeMoveLineIds) : []);
        }

        foreach ($reservedPerLocation ?? [] as $locationId => $reserved) {
            $occupancy[$locationId] = ($occupancy[$locationId] ?? 0) + $reserved;
        }

        return $occupancy;
    }

    protected function packageOccupancy(Collection $candidates, int $packageTypeId, array $excludeMoveLineIds): array
    {
        $occupancy = MoveLine::query()
            ->whereNotIn('id', $excludeMoveLineIds)
            ->whereHas('resultPackage', fn ($query) => $query->where('package_type_id', $packageTypeId))
            ->whereNotIn('state', [MoveState::DRAFT, MoveState::CANCELED, MoveState::DONE])
            ->groupBy('destination_location_id')
            ->selectRaw('destination_location_id, COUNT(DISTINCT result_package_id) as count')
            ->pluck('count', 'destination_location_id')
            ->all();

        $stored = ProductQuantity::query()
            ->whereHas('package', fn ($query) => $query->where('package_type_id', $packageTypeId))
            ->whereIn('location_id', $candidates->pluck('id'))
            ->groupBy('location_id')
            ->selectRaw('location_id, COUNT(DISTINCT package_id) as count')
            ->pluck('count', 'location_id')
            ->all();

        foreach ($stored as $locationId => $count) {
            $occupancy[$locationId] = ($occupancy[$locationId] ?? 0) + $count;
        }

        return $occupancy;
    }

    protected function productOccupancy(Collection $candidates, Product $product, array $excludeMoveLineIds): array
    {
        $occupancy = ProductQuantity::query()
            ->where('product_id', $product->id)
            ->whereIn('location_id', $candidates->pluck('id'))
            ->groupBy('location_id')
            ->selectRaw('location_id, SUM(quantity) as total')
            ->pluck('total', 'location_id')
            ->all();

        $incoming = MoveLine::query()
            ->whereNotIn('id', $excludeMoveLineIds)
            ->where('product_id', $product->id)
            ->whereIn('destination_location_id', $candidates->pluck('id'))
            ->whereNotIn('state', [MoveState::DRAFT, MoveState::DONE, MoveState::CANCELED])
            ->get();

        foreach ($incoming as $moveLine) {
            $occupancy[$moveLine->destination_location_id] = ($occupancy[$moveLine->destination_location_id] ?? 0)
                + $moveLine->uom->computeQuantity($moveLine->quantity, $product->uom);
        }

        return $occupancy;
    }

    protected function firstAvailablePutawayLocation(
        Collection $rules,
        ?Product $product,
        float $quantity,
        ?Package $package,
        ?Packaging $packaging,
        array $occupancy
    ): ?Location {
        $packageType = $package?->packageType ?? $packaging?->packageType;

        $rejected = collect();

        foreach ($rules as $rule) {
            $target = $this->putawayRuleTarget($rule, $product);

            if (! $rule->storage_category_id) {
                if ($rejected->contains('id', $target->id)) {
                    continue;
                }

                if ($target->acceptsStorageOf($product, $quantity, $package, $occupancy[$target->id] ?? 0)) {
                    return $target;
                }

                continue;
            }

            $shelves = $target->internalDescendants()
                ->filter(fn (Location $shelf) => $shelf->storage_category_id === $rule->storage_category_id);

            foreach ($shelves as $shelf) {
                if ($rejected->contains('id', $shelf->id) || ! $this->alreadyStocks($shelf, $packageType, $product, $occupancy)) {
                    continue;
                }

                if ($shelf->acceptsStorageOf($product, $quantity, $packageType ? $package : null, $occupancy[$shelf->id] ?? 0)) {
                    return $shelf;
                }

                $rejected->push($shelf);
            }

            foreach ($shelves as $shelf) {
                if ($rejected->contains('id', $shelf->id)) {
                    continue;
                }

                if ($shelf->acceptsStorageOf($product, $quantity, $package, $occupancy[$shelf->id] ?? 0)) {
                    return $shelf;
                }

                $rejected->push($shelf);
            }
        }

        return null;
    }

    protected function putawayRuleTarget(PutawayRule $rule, ?Product $product): Location
    {
        $target = $rule->outLocation;

        if ($rule->sub_location !== SubLocation::LAST_USED) {
            return $target;
        }

        $lastUsed = MoveLine::query()
            ->where('state', MoveState::DONE)
            ->when($product, fn ($query) => $query->where('product_id', $product->id))
            ->whereHas('destinationLocation', fn ($query) => $query
                ->whereKey($target->id)
                ->orWhere('parent_path', 'like', $target->parent_path.'%'))
            ->when($rule->packageTypes->isNotEmpty(), fn ($query) => $query
                ->whereHas('resultPackage', fn ($package) => $package
                    ->whereIn('package_type_id', $rule->packageTypes->pluck('id')->all())))
            ->orderByDesc('scheduled_at')
            ->first()
            ?->destinationLocation;

        return $lastUsed ?? $target;
    }

    protected function alreadyStocks(Location $shelf, ?PackageType $packageType, ?Product $product, array $occupancy): bool
    {
        if ($packageType) {
            return $shelf->quantities->some(
                fn ($stock) => $stock->package_id && $stock->package?->package_type_id === $packageType->id
            );
        }

        return float_compare(
            $occupancy[$shelf->id] ?? 0,
            0,
            precisionRounding: $product?->uom->rounding ?? 0.01
        ) > 0;
    }

    public function acceptsStorageOf(
        ?Product $product,
        float $quantity = 0,
        ?Package $package = null,
        float $locationQty = 0,
        array $excludeMoveLineIds = []
    ): bool {
        if (! $this->storage_category_id) {
            return true;
        }

        if (! $this->withinWeightLimit($product, $quantity, $package, $excludeMoveLineIds)) {
            return false;
        }

        if (! $this->withinCapacityLimit($product, $quantity, $package, $locationQty)) {
            return false;
        }

        return $this->acceptsProductMix($product);
    }

    protected function withinWeightLimit(?Product $product, float $quantity, ?Package $package, array $excludeMoveLineIds): bool
    {
        $maxWeight = $this->storageCategory->max_weight;

        if (! $maxWeight) {
            return true;
        }

        $storedWeight = $this->quantities->sum(
            fn ($stock) => ($stock->quantity ?? 0) * ($stock->product?->weight ?? 0)
        );

        $incomingWeight = MoveLine::query()
            ->whereNotIn('id', $excludeMoveLineIds)
            ->where('destination_location_id', $this->id)
            ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
            ->get()
            ->sum(fn ($moveLine) => $moveLine->qty * ($moveLine->product?->weight ?? 0));

        $addedWeight = $package?->package_type_id
            ? MoveLine::query()
                ->where('result_package_id', $package->id)
                ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
                ->get()
                ->sum(fn ($moveLine) => $moveLine->qty * ($moveLine->product?->weight ?? 0))
            : ($product?->weight ?? 0) * $quantity;

        return $maxWeight >= $storedWeight + $incomingWeight + $addedWeight;
    }

    protected function withinCapacityLimit(?Product $product, float $quantity, ?Package $package, float $locationQty): bool
    {
        if ($package?->package_type_id) {
            $capacity = $this->storageCategory->storageCategoryCapacitiesByPackageType
                ->first(fn ($entry) => $entry->package_type_id === $package->package_type_id);

            return ! $capacity || $locationQty < $capacity->qty;
        }

        $capacity = $this->storageCategory->storageCategoryCapacitiesByProduct
            ->first(fn ($entry) => $entry->product_id === $product?->id);

        if (! $capacity) {
            return true;
        }

        return $locationQty < $capacity->qty && $quantity + $locationQty <= $capacity->qty;
    }

    protected function acceptsProductMix(?Product $product): bool
    {
        $policy = $this->storageCategory->allow_new_products;

        if ($policy !== AllowNewProduct::EMPTY && $policy !== AllowNewProduct::SAME) {
            return true;
        }

        $stocked = $this->quantities->filter(
            fn ($stock) => float_compare($stock->quantity ?? 0, 0, precisionRounding: $stock->product?->uom?->rounding ?? 0.01) > 0
        );

        if ($policy === AllowNewProduct::EMPTY) {
            return $stocked->isEmpty();
        }

        if ($stocked->contains(fn ($stock) => $stock->product_id !== $product->id)) {
            return false;
        }

        return ! MoveLine::query()
            ->where('product_id', '!=', $product->id)
            ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
            ->where('destination_location_id', $this->id)
            ->exists();
    }

    public function isDescendantOf(self $ancestor): bool
    {
        return Str::startsWith($this->parent_path, $ancestor->parent_path);
    }

    public function bypassesReservation(): bool
    {
        return in_array($this->type, [
            LocationType::SUPPLIER,
            LocationType::CUSTOMER,
            LocationType::INVENTORY,
            LocationType::PRODUCTION,
        ]) || $this->is_scrap;
    }

    public function updateFullName()
    {
        if ($this->type === LocationType::VIEW) {
            $this->full_name = $this->name;
        } else {
            $this->full_name = $this->parent
                ? $this->parent->full_name.'/'.$this->name
                : $this->name;
        }
    }

    public function ancestorIds(): array
    {
        return collect(explode('/', (string) $this->parent_path))
            ->filter(fn ($id) => $id !== '' && (int) $id !== (int) $this->id)
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function updateParentPath()
    {
        if ($this->type === LocationType::VIEW) {
            $this->parent_path = $this->id.'/';
        } else {
            $this->parent_path = $this->parent
                ? $this->parent->parent_path.$this->id.'/'
                : $this->id.'/';
        }
    }

    public function updateChildrenFullNames(): void
    {
        $children = $this->children()->getModel()
            ->withTrashed()
            ->where('parent_id', $this->id)
            ->get();

        $children->each(function ($child) {
            $child->updateFullName();
            $child->saveQuietly();

            $child->updateChildrenFullNames();
        });
    }

    public function updateChildrenParentPaths(): void
    {
        $children = $this->children()->getModel()
            ->withTrashed()
            ->where('parent_id', $this->id)
            ->get();

        $children->each(function ($child) {
            $child->updateParentPath();
            $child->saveQuietly();

            $child->updateChildrenParentPaths();
        });
    }

    protected static function newFactory(): LocationFactory
    {
        return LocationFactory::new();
    }
}
