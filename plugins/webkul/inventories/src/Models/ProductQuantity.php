<?php

namespace Webkul\Inventory\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\ProductQuantityFactory;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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

    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity - $this->reserved_quantity;
    }

    public function updateScheduledAt()
    {
        $this->scheduled_at = Carbon::create(
            now()->year,
            app(OperationSettings::class)->annual_inventory_month,
            app(OperationSettings::class)->annual_inventory_day,
            0,
            0,
            0
        );

        if ($this->location?->cyclic_inventory_frequency) {
            $this->scheduled_at = now()->addDays($this->location->cyclic_inventory_frequency);
        }
    }

    protected static function newFactory(): ProductQuantityFactory
    {
        return ProductQuantityFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productQuantity) {
            $productQuantity->creator_id ??= Auth::id();
        });

        static::saving(function ($productQuantity) {
            $productQuantity->updateScheduledAt();
        });
    }
}
