<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Database\Factories\OrderGroupFactory;
use Webkul\Security\Models\User;

class OrderGroup extends Model
{
    use HasFactory;

    protected $table = 'purchases_order_groups';

    protected $fillable = [
        'creator_id',
    ];

    protected $casts = [];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): OrderGroupFactory
    {
        return OrderGroupFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->creator_id ??= Auth::id();
        });
    }
}
