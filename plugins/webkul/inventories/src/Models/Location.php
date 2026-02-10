<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\LocationFactory;
use Webkul\Inventory\Enums\LocationType;
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

            $category->updateParentPath();
            $category->updateFullName();
        });

        static::saving(function ($category) {
            if (! empty($category->cyclic_inventory_frequency)) {
                $category->next_inventory_date = now()->addDays(
                    (int) $category->cyclic_inventory_frequency
                );
            } else {
                $category->next_inventory_date = null;
            }
        });

        static::updated(function ($category) {
            $category->updateChildrenParentPaths();

            if ($category->wasChanged('full_name')) {
                $category->updateChildrenFullNames();
            }
        });
    }
}
