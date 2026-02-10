<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Inventory\Database\Factories\StorageCategoryFactory;
use Webkul\Inventory\Enums\AllowNewProduct;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class StorageCategory extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'inventories_storage_categories';

    protected $fillable = [
        'name',
        'sort',
        'allow_new_products',
        'parent_path',
        'max_weight',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'allow_new_products' => AllowNewProduct::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function storageCategoryCapacities(): HasMany
    {
        return $this->hasMany(StorageCategoryCapacity::class, 'storage_category_id');
    }

    public function storageCategoryCapacitiesByProduct(): HasMany
    {
        return $this->storageCategoryCapacities()->whereNotNull('product_id');
    }

    public function storageCategoryCapacitiesByPackageType(): HasMany
    {
        return $this->storageCategoryCapacities()->whereNotNull('package_type_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): StorageCategoryFactory
    {
        return StorageCategoryFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($storageCategory) {
            $storageCategory->creator_id ??= Auth::id();
        });
    }
}
