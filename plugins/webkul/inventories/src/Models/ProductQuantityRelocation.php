<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\ProductQuantityRelocationFactory;
use Webkul\Security\Models\User;

class ProductQuantityRelocation extends Model
{
    use HasFactory;

    protected $table = 'inventories_product_quantity_relocations';

    protected $fillable = [
        'description',
        'destination_location_id',
        'destination_package_id',
        'creator_id',
    ];

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function destinationPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): ProductQuantityRelocationFactory
    {
        return ProductQuantityRelocationFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productQuantityRelocation) {
            $productQuantityRelocation->creator_id ??= Auth::id();
        });
    }
}
