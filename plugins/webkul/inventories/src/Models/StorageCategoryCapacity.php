<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\StorageCategoryCapacityFactory;
use Webkul\Security\Models\User;

class StorageCategoryCapacity extends Model
{
    protected $table = 'inventories_storage_category_capacities';

    protected $fillable = [
        'qty',
        'product_id',
        'storage_category_id',
        'package_type_id',
        'creator_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function storageCategory(): BelongsTo
    {
        return $this->belongsTo(StorageCategory::class);
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): StorageCategoryCapacityFactory
    {
        return StorageCategoryCapacityFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($storageCategoryCapacity) {
            $storageCategoryCapacity->creator_id ??= Auth::id();
        });
    }
}
