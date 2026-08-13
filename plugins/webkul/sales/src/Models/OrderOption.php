<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

class OrderOption extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'sales_order_options';

    protected $fillable = [
        'sort',
        'order_id',
        'product_id',
        'line_id',
        'uom_id',
        'creator_id',
        'name',
        'quantity',
        'price_unit',
        'discount',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    protected $appends = [
        'is_present',
    ];

    public function isPresent(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => ! is_null($this->line_id),
        );
    }

    public function linkMatchingLine(): void
    {
        $lineId = OrderLine::where('order_id', $this->order_id)
            ->where('product_id', $this->product_id)
            ->whereNull('display_type')
            ->value('id');

        if ($this->line_id == $lineId) {
            return;
        }

        $this->line_id = $lineId;

        $this->saveQuietly();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function line()
    {
        return $this->belongsTo(OrderLine::class, 'line_id');
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderOption) {
            $orderOption->creator_id ??= Auth::id();
        });

        static::saving(function ($orderOption) {
            if (blank($orderOption->name)) {
                $orderOption->name = $orderOption->product?->name;
            }
        });

        static::created(function ($orderOption) {
            $orderOption->linkMatchingLine();
        });

        static::updated(function ($orderOption) {
            if ($orderOption->wasChanged('product_id')) {
                $orderOption->linkMatchingLine();
            }
        });
    }
}
