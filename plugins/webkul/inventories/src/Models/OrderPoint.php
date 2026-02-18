<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\OrderPointFactory;
use Webkul\Inventory\Enums\OrderPointTrigger;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class OrderPoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventories_order_points';

    protected $fillable = [
        'name',
        'trigger',
        'snoozed_until',
        'product_min_qty',
        'product_max_qty',
        'qty_multiple',
        'qty_to_order_manual',
        'product_id',
        'product_category_id',
        'warehouse_id',
        'location_id',
        'route_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'trigger' => OrderPointTrigger::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): OrderPointFactory
    {
        return OrderPointFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderPoint) {
            $orderPoint->creator_id ??= Auth::id();
        });
    }
}
