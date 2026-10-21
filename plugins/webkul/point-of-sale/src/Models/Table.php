<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\PointOfSale\Database\Factories\TableFactory;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\TableShape;
use Webkul\Security\Models\User;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pos_tables';

    protected $fillable = [
        'table_number',
        'shape',
        'position_h',
        'position_v',
        'width',
        'height',
        'seats',
        'color',
        'floor_id',
        'parent_id',
        'creator_id',
    ];

    protected $casts = [
        'shape'      => TableShape::class,
        'position_h' => 'decimal:4',
        'position_v' => 'decimal:4',
        'width'      => 'decimal:4',
        'height'     => 'decimal:4',
    ];

    protected $attributes = [
        'shape'      => 'square',
        'position_h' => 0,
        'position_v' => 0,
        'width'      => 50,
        'height'     => 50,
        'seats'      => 2,
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function openOrders(): HasMany
    {
        return $this->orders()->where('state', OrderState::DRAFT);
    }

    public function openAmount(): float
    {
        return (float) $this->openOrders()->sum('amount_total');
    }

    public function isOccupied(): bool
    {
        return $this->openOrders()->exists();
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Table $table) {
            $table->computeCreatorId();
        });
    }

    protected static function newFactory(): TableFactory
    {
        return TableFactory::new();
    }
}
