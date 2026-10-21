<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Models\Lot;
use Webkul\PointOfSale\Database\Factories\OrderLineLotFactory;
use Webkul\Security\Models\User;

class OrderLineLot extends Model
{
    use HasFactory;

    protected $table = 'pos_order_line_lots';

    protected $fillable = [
        'lot_name',
        'qty',
        'order_line_id',
        'lot_id',
        'creator_id',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
    ];

    protected $attributes = [
        'qty' => 0,
    ];

    public function orderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class, 'order_line_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (OrderLineLot $lot) {
            $lot->computeCreatorId();
        });
    }

    protected static function newFactory(): OrderLineLotFactory
    {
        return OrderLineLotFactory::new();
    }
}
