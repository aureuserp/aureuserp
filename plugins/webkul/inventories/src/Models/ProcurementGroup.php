<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Database\Factories\ProcurementGroupFactory;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Order as SaleOrder;
use Webkul\Security\Models\User;

class ProcurementGroup extends Model
{
    use HasFactory;

    protected $table = 'inventories_procurement_groups';

    protected $fillable = [
        'name',
        'move_type',
        'partner_id',
        'creator_id',
        'sale_order_id',
    ];

    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($procurementGroup) {
            $procurementGroup->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): ProcurementGroupFactory
    {
        return ProcurementGroupFactory::new();
    }
}
