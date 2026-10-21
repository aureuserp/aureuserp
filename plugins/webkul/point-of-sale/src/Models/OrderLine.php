<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Tax;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Warehouse;
use Webkul\PointOfSale\Database\Factories\OrderLineFactory;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;
use Webkul\Support\Traits\BelongsToCompany;

class OrderLine extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'pos_order_lines';

    protected $fillable = [
        'uuid',
        'name',
        'full_product_name',
        'sort',
        'qty',
        'price_unit',
        'price_extra',
        'price_type',
        'discount',
        'price_subtotal',
        'price_subtotal_incl',
        'price_tax',
        'unit_cost',
        'total_cost',
        'margin',
        'margin_percent',
        'refunded_qty',
        'customer_note',
        'note',
        'is_cost_computed',
        'is_edited',
        'is_skipped_in_preparation',
        'order_id',
        'product_id',
        'uom_id',
        'price_list_id',
        'refunded_order_line_id',
        'account_move_line_id',
        'route_id',
        'warehouse_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'price_type'                => PriceType::class,
        'qty'                       => 'decimal:4',
        'price_unit'                => 'decimal:4',
        'price_extra'               => 'decimal:4',
        'discount'                  => 'decimal:2',
        'price_subtotal'            => 'decimal:4',
        'price_subtotal_incl'       => 'decimal:4',
        'price_tax'                 => 'decimal:4',
        'unit_cost'                 => 'decimal:4',
        'total_cost'                => 'decimal:4',
        'margin'                    => 'decimal:4',
        'margin_percent'            => 'decimal:4',
        'refunded_qty'              => 'decimal:4',
        'is_cost_computed'          => 'boolean',
        'is_edited'                 => 'boolean',
        'is_skipped_in_preparation' => 'boolean',
    ];

    protected $attributes = [
        'qty'                       => 0,
        'price_unit'                => 0,
        'price_extra'               => 0,
        'price_type'                => 'original',
        'discount'                  => 0,
        'price_subtotal'            => 0,
        'price_subtotal_incl'       => 0,
        'price_tax'                 => 0,
        'unit_cost'                 => 0,
        'total_cost'                => 0,
        'margin'                    => 0,
        'margin_percent'            => 0,
        'refunded_qty'              => 0,
        'is_cost_computed'          => false,
        'is_edited'                 => false,
        'is_skipped_in_preparation' => false,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class)->withTrashed();
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'pos_order_line_taxes', 'order_line_id', 'tax_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'pos_order_line_attribute_values',
            'order_line_id',
            'attribute_value_id',
        );
    }

    public function lots(): HasMany
    {
        return $this->hasMany(OrderLineLot::class, 'order_line_id');
    }

    public function refundedOrderLine(): BelongsTo
    {
        return $this->belongsTo(self::class, 'refunded_order_line_id');
    }

    public function refundLines(): HasMany
    {
        return $this->hasMany(self::class, 'refunded_order_line_id');
    }

    public function accountMoveLine(): BelongsTo
    {
        return $this->belongsTo(MoveLine::class, 'account_move_line_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class)->withTrashed();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function refundableQty(): float
    {
        return abs((float) $this->qty) - (float) $this->refunded_qty;
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeRefundedQty(): void
    {
        $refunded = static::withoutGlobalScopes()
            ->where('refunded_order_line_id', $this->getKey())
            ->whereHas('order', fn (Builder $query) => $query
                ->withoutGlobalScopes()
                ->where('state', '!=', OrderState::CANCELED))
            ->sum('qty');

        $this->refunded_qty = float_round(-1 * (float) $refunded, precisionDigits: 4);
    }

    public function computeUuid(): void
    {
        $this->uuid ??= Str::uuid()->toString();
    }

    public function inheritFromOrder(): void
    {
        $order = $this->order;

        if (! $order) {
            return;
        }

        $this->company_id ??= $order->company_id;

        $this->price_list_id ??= $order->price_list_id;
    }

    public function computeProductDefaults(): void
    {
        $product = $this->product;

        if (! $product) {
            return;
        }

        $this->name ??= $product->name;

        $this->full_product_name ??= $product->name;

        $this->uom_id ??= $product->uom_id;
    }

    public function computeSort(): void
    {
        if ($this->sort) {
            return;
        }

        $this->sort = (int) static::withoutGlobalScopes()
            ->where('order_id', $this->order_id)
            ->max('sort') + 1;
    }

    public function syncRefundedOrderLine(): void
    {
        $refunded = $this->refunded_order_line_id
            ? static::withoutGlobalScopes()->find($this->refunded_order_line_id)
            : null;

        if (! $refunded) {
            return;
        }

        $refunded->computeRefundedQty();

        $refunded->saveQuietly();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (OrderLine $line) {
            $line->computeCreatorId();

            $line->computeUuid();

            $line->computeSort();
        });

        static::saving(function (OrderLine $line) {
            $line->inheritFromOrder();

            $line->computeProductDefaults();
        });

        static::saved(function (OrderLine $line) {
            $line->syncRefundedOrderLine();
        });

        static::deleted(function (OrderLine $line) {
            $line->syncRefundedOrderLine();
        });
    }

    protected static function newFactory(): OrderLineFactory
    {
        return OrderLineFactory::new();
    }
}
