<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Security\Models\User;

class Product extends BaseProduct
{
    use HasCustomFields;

    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'sale_delay',
            'tracking',
            'description_picking',
            'description_pickingout',
            'description_pickingin',
            'is_storable',
            'expiration_time',
            'use_time',
            'removal_time',
            'alert_time',
            'use_expiration_date',
            'responsible_id',
        ]);

        $this->mergeCasts([
            'tracking'            => ProductTracking::class,
            'use_expiration_date' => 'boolean',
            'is_storable'         => 'boolean',
        ]);

        parent::__construct($attributes);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_product_routes', 'product_id', 'route_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function quantities(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(ProductQuantity::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(ProductQuantity::class);
        }
    }

    public function moves(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(Move::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(Move::class);
        }
    }

    public function moveLines(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(MoveLine::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(MoveLine::class);
        }
    }

    public function storageCategoryCapacities(): BelongsToMany
    {
        return $this->belongsToMany(StorageCategoryCapacity::class, 'inventories_storage_category_capacities', 'storage_category_id', 'package_type_id');
    }

    public function orderPoints(): HasMany
    {
        return $this->hasMany(OrderPoint::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getOnHandQuantityAttribute(): float
    {
        return $this->quantities()
            ->whereHas('location', function ($query) {
                $query->where('type', LocationType::INTERNAL)
                    ->where('is_scrap', false);
            })
            ->sum('quantity');
    }
}
