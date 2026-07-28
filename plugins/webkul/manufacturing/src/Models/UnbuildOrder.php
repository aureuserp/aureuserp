<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Models\Location;
use Webkul\Manufacturing\Database\Factories\UnbuildOrderFactory;
use Webkul\Manufacturing\Enums\UnbuildOrderState;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class UnbuildOrder extends Model
{
    use HasFactory;

    protected $table = 'manufacturing_unbuild_orders';

    protected $fillable = [
        'name',
        'state',
        'quantity',
        'product_id',
        'company_id',
        'uom_id',
        'bill_of_material_id',
        'manufacturing_order_id',
        'lot_id',
        'location_id',
        'destination_location_id',
        'creator_id',
    ];

    protected $casts = [
        'state'    => UnbuildOrderState::class,
        'quantity' => 'decimal:4',
    ];

    public function getModelTitle(): string
    {
        return __('manufacturing::models/unbuild-order.title');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class)->withTrashed();
    }

    public function billOfMaterial(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_id')->withTrashed();
    }

    public function manufacturingOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'manufacturing_order_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id')->withTrashed();
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id')->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): UnbuildOrderFactory
    {
        return UnbuildOrderFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $unbuildOrder): void {
            $authUser = Auth::user();

            $unbuildOrder->creator_id ??= $authUser?->id;
            $unbuildOrder->company_id ??= $authUser?->default_company_id;
            $unbuildOrder->state ??= UnbuildOrderState::DRAFT;
        });
    }
}
