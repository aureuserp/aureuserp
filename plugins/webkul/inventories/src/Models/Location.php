<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Inventory\Database\Factories\LocationFactory;
use Webkul\Inventory\Enums\AllowNewProduct;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\SubLocation;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Product\Enums\ProductRemoval;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Location extends Model
{
    use HasFactory, SoftDeletes;

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

    public function getInternalChildLocations()
    {
        return static::where('type', LocationType::INTERNAL)
            ->whereRaw('parent_path LIKE ?', [$this->parent_path . '%'])
            ->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->creator_id ??= Auth::id();

            if ($category->parent_id) {
                $parentLocation = Location::find($category->parent_id);
                $category->warehouse_id = $parentLocation?->warehouse_id;
            } else {
                $category->warehouse_id = null;
            }

            if (! empty($category->cyclic_inventory_frequency)) {
                $category->next_inventory_date = now()->addDays(
                    (int) $category->cyclic_inventory_frequency
                );
            } else {
                $category->next_inventory_date = null;
            }
        });

        static::created(function ($category) {
            $category->updateParentPath();

            $category->updateFullName();

            $category->saveQuietly();
        });

        static::saving(function ($category) {
            if (! empty($category->cyclic_inventory_frequency)) {
                $category->next_inventory_date = now()->addDays((int) $category->cyclic_inventory_frequency);
            } else {
                $category->next_inventory_date = null;
            }

            $category->updateParentPath();

            $category->updateFullName();
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
                    throw new \Exception("You cannot set a location as a scrap location when it is assigned as a destination location for a manufacturing type operation.");
                }
            }

            if ($location->isDirty('company_id')) {
                throw new \Exception("Changing the company of this record is forbidden at this point, you should rather archive it and create a new one.");
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

        static::updated(function ($category) {
            $category->updateChildrenParentPaths();

            if ($category->wasChanged('full_name')) {
                $category->updateChildrenFullNames();
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

            $childrenLocations = Location::withTrashed()
                ->whereRaw('parent_path LIKE ?', [$location->parent_path . '%'])
                ->where('id', '!=', $location->id)
                ->get();

            $internalChildrenLocationIds = $childrenLocations->filter(
                fn ($childLocation) => $childLocation->type === LocationType::INTERNAL
            )->pluck('id')->push($location->id)->all();

            $childrenQuantities = ProductQuantity::where(function ($q) {
                    $q->where('quantity', '!=', 0)
                        ->orWhere('reserved_quantity', '!=', 0);
                })
                ->whereIn('location_id', $internalChildrenLocationIds)
                ->get();

            if ($childrenQuantities->isNotEmpty()) {
                $locationNames = $childrenQuantities->pluck('location.name')->unique()->implode(', ');

                throw new \Exception(__("You can't disable locations :locations because they still contain products.", [
                    'locations' => $locationNames,
                ]));
            }

            $childrenLocations->each(fn ($childLocation) => $childLocation->delete());
        });

        static::forceDeleting(function (Location $location) {
            Location::withTrashed()
                ->whereRaw('parent_path LIKE ?', [$location->parent_path . '%'])
                ->where('id', '!=', $location->id)
                ->get()
                ->each(fn ($childLocation) => $childLocation->forceDelete());
        });

        static::restored(function (Location $location) {
            Location::withTrashed()
                ->whereRaw('parent_path LIKE ?', [$location->parent_path . '%'])
                ->where('id', '!=', $location->id)
                ->get()
                ->each(fn ($childLocation) => $childLocation->restore());
        });
    }

    public function getPutawayStrategy(
        Product $product,
        float $quantity = 0,
        ?Package $package = null,
        ?Packaging $packaging = null,
        ?array $additionalQty = null,
        array $excludeMoveLineIds = []
    ): Location {
        $packageType = $package?->packageType ?? $packaging?->packageType;

        $categoryIds = collect();

        $current = Category::find($product->category_id);

        while ($current) {
            $categoryIds->push($current->id);
            
            $current = $current->parent_id ? $current->parent : null;
        }

        $putawayRules = $this->putawayRules
            ->filter(fn ($rule) => (! $rule->product_id || $rule->product_id === $product->id)
                && (! $rule->category_id || $categoryIds->contains($rule->category_id))
                && (! $rule->packageTypes->isNotEmpty() || ($packageType && $rule->packageTypes->contains('id', $packageType->id)))
            )
            ->sortByDesc(fn ($rule) => [
                $rule->packageTypes->isNotEmpty() ? 1 : 0,
                $rule->product_id ? 1 : 0,
                $rule->category_id === $categoryIds->first() ? 1 : 0,
                $rule->category_id ? 1 : 0,
            ]);

        $locations = $this->getInternalChildLocations();

        $putawayLocation = null;

        if ($putawayRules->isNotEmpty()) {
            $qtyByLocation = [];

            if ($locations->pluck('storage_category_id')->filter()->isNotEmpty()) {
                if ($package && $package->package_type_id) {
                    $qtyByLocation = MoveLine::whereNotIn('id', $excludeMoveLineIds)
                        ->whereHas('resultPackage', fn ($query) => $query->where('package_type_id', $packageType?->id))
                        ->whereNotIn('state', [MoveState::DRAFT, MoveState::CANCELED, MoveState::DONE])
                        ->groupBy('destination_location_id')
                        ->selectRaw('destination_location_id, COUNT(DISTINCT result_package_id) as count')
                        ->pluck('count', 'destination_location_id')
                        ->all();

                    $packageQuantities = ProductQuantity::whereHas('package', fn ($query) => $query->where('package_type_id', $packageType?->id))
                        ->whereIn('location_id', $locations->pluck('id'))
                        ->groupBy('location_id')
                        ->selectRaw('location_id, COUNT(DISTINCT package_id) as count')
                        ->pluck('count', 'location_id')
                        ->all();

                    foreach ($packageQuantities as $locationId => $count) {
                        $qtyByLocation[$locationId] = ($qtyByLocation[$locationId] ?? 0) + $count;
                    }
                } else {
                    $qtyByLocation = ProductQuantity::where('product_id', $product->id)
                        ->whereIn('location_id', $locations->pluck('id'))
                        ->groupBy('location_id')
                        ->selectRaw('location_id, SUM(quantity) as total')
                        ->pluck('total', 'location_id')
                        ->all();

                    $moveLines = MoveLine::whereNotIn('id', $excludeMoveLineIds)
                        ->where('product_id', $product->id)
                        ->whereIn('destination_location_id', $locations->pluck('id'))
                        ->whereNotIn('state', [MoveState::DRAFT, MoveState::DONE, MoveState::CANCELED])
                        ->get();

                    foreach ($moveLines as $moveLine) {
                        $currentQty = $moveLine->uom->computeQuantity($moveLine->quantity, $product->uom);

                        $qtyByLocation[$moveLine->destination_location_id] = ($qtyByLocation[$moveLine->destination_location_id] ?? 0) + $currentQty;
                    }
                }
            }

            foreach ($additionalQty ?? [] as $locationId => $qty) {
                $qtyByLocation[$locationId] = ($qtyByLocation[$locationId] ?? 0) + $qty;
            }

            $putawayLocation = $this->getPutawayLocation($putawayRules, $product, $quantity, $package, $packaging, $qtyByLocation);
        }

        return $putawayLocation
            ?? (($locations->isNotEmpty() && $this->type === LocationType::VIEW) ? $locations->first() : $this);
    }

    public function getPutawayLocation(
        mixed $putawayRules,
        Product $product,
        float $quantity = 0,
        ?Package $package = null,
        ?Packaging $packaging = null,
        array $qtyByLocation = []
    ): ?Location {
        $packageType = $package?->packageType ?? $packaging?->packageType;

        $checkedLocations = collect();

        foreach ($putawayRules as $putawayRule) {
            $outLocation = $putawayRule->outLocation;

            if ($putawayRule->sub_location === SubLocation::LAST_USED) {
                $lastUsedLocation = MoveLine::where('state', MoveState::DONE)
                    ->where('product_id', $product->id)
                    ->whereHas('destinationLocation', fn ($q) => $q->where('id', $this->locationOut->id)
                        ->orWhereRaw('parent_path LIKE ?', [$this->locationOut->parent_path . '%'])
                    )
                    ->when($putawayRule->packageTypes->isNotEmpty(), function ($query) use ($putawayRule) {
                        $query->whereHas('resultPackage', fn ($q) => $q->whereIn('package_type_id', $putawayRule->packageTypes->pluck('id')->all()));
                    })
                    ->orderBy('scheduled_at', 'desc')
                    ->first()
                    ?->destinationLocation;
                
                $outLocation = $lastUsedLocation ?? $outLocation;
            }

            $childLocations = $outLocation->getInternalChildLocations();

            if (! $putawayRule->storage_category_id) {
                if ($checkedLocations->contains('id', $outLocation->id)) {
                    continue;
                }

                if ($outLocation->canBeUsed($product, $quantity, $package, $qtyByLocation[$outLocation->id] ?? 0)) {
                    return $outLocation;
                }

                continue;
            }

            $childLocations = $childLocations->filter(
                fn ($location) => $location->storage_category_id === $putawayRule->storage_category_id
            );

            foreach ($childLocations as $location) {
                if ($checkedLocations->contains('id', $location->id)) {
                    continue;
                }

                if ($packageType) {
                    $hasPackageType = $location->quantities->some(
                        fn ($quantity) => $quantity->package_id && $quantity->package?->package_type_id === $packageType->id
                    );

                    if ($hasPackageType) {
                        if ($location->canBeUsed($product, $quantity, package: $package, locationQty: $qtyByLocation[$location->id] ?? 0)) {
                            return $location;
                        }

                        $checkedLocations->push($location);
                    }
                } elseif (float_compare($qtyByLocation[$location->id] ?? 0, 0, precisionRounding: $product->uom->rounding) > 0) {
                    if ($location->canBeUsed($product, $quantity, locationQty: $qtyByLocation[$location->id] ?? 0)) {
                        return $location;
                    }

                    $checkedLocations->push($location);
                }
            }

            foreach ($childLocations->filter(fn ($location) => $location->storage_category_id === $putawayRule->storage_category_id) as $location) {
                if ($checkedLocations->contains('id', $location->id)) {
                    continue;
                }

                if ($location->canBeUsed($product, $quantity, $package, $qtyByLocation[$location->id] ?? 0)) {
                    return $location;
                }

                $checkedLocations->push($location);
            }
        }

        return null;
    }

    public function canBeUsed(
        Product $product,
        float $quantity = 0,
        ?Package $package = null,
        float $locationQty = 0,
        array $excludeMoveLineIds = []
    ): bool
    {
        if (! $this->storage_category_id) {
            return true;
        }

        $currentWeight = $this->quantities
            ->sum(fn ($quantity) => ($quantity->quantity ?? 0) * ($quantity->product?->weight ?? 0));

        $incomingWeight = MoveLine::whereNotIn('id', $excludeMoveLineIds)
            ->where('destination_location_id', $this->id)
            ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
            ->get()
            ->sum(fn ($moveLine) => $moveLine->qty * ($moveLine->product?->weight ?? 0));

        $forecastWeight = $currentWeight + $incomingWeight;

        if ($package && $package->package_type_id) {
            $packageWeight = MoveLine::where('result_package_id', $package->id)
                ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
                ->get()
                ->sum(fn ($moveLine) => $moveLine->qty * ($moveLine->product?->weight ?? 0));

            if ($this->storageCategory->max_weight && $this->storageCategory->max_weight < $forecastWeight + $packageWeight) {
                return false;
            }

            $packageCapacity = $this->storageCategory->storageCategoryCapacitiesByPackageType
                ->first(fn ($pc) => $pc->package_type_id === $package->package_type_id);

            if ($packageCapacity && $locationQty >= $packageCapacity->qty) {
                return false;
            }
        } else {
            if ($this->storageCategory->max_weight && $this->storageCategory->max_weight < $forecastWeight + ($product->weight ?? 0) * $quantity) {
                return false;
            }

            $productCapacity = $this->storageCategory->storageCategoryCapacitiesByProduct
                ->first(fn ($pc) => $pc->product_id === $product->id);

            if ($productCapacity && $locationQty >= $productCapacity->qty) {
                return false;
            }

            if ($productCapacity && $quantity + $locationQty > $productCapacity->qty) {
                return false;
            }
        }

        $positiveQuantities = $this->quantities->filter(
            fn ($quantity) => float_compare($quantity->quantity ?? 0, 0, precisionRounding: $quantity->product?->uom?->rounding ?? 0.01) > 0
        );

        if (
            $this->storageCategory->allow_new_products === AllowNewProduct::EMPTY
            && $positiveQuantities->isNotEmpty()
        ) {
            return false;
        }

        if ($this->storageCategory->allow_new_products === AllowNewProduct::SAME) {
            if (
                $positiveQuantities->isNotEmpty()
                && $positiveQuantities->filter(fn ($quantity) => $quantity->product_id !== $product->id)->isNotEmpty()
            ) {
                return false;
            }

            $hasDifferentProduct = MoveLine::where('product_id', '!=', $product->id)
                ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
                ->where('destination_location_id', $this->id)
                ->exists();

            if ($hasDifferentProduct) {
                return false;
            }
        }

        return true;
    }

    public function isChildOf(self $otherLocation): bool
    {
        return Str::startsWith($this->parent_path, $otherLocation->parent_path);
    }

    public function shouldBypassReservation(): bool
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
