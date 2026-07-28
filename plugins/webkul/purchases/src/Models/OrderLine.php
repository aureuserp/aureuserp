<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Tax;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move as InventoryMove;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\ProcurementGroup;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Packaging;
use Webkul\Purchase\Database\Factories\OrderLineFactory;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class OrderLine extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'purchases_order_lines';

    protected $fillable = [
        'name',
        'state',
        'sort',
        'qty_received_method',
        'display_type',
        'product_qty',
        'product_uom_qty',
        'product_packaging_qty',
        'price_tax',
        'discount',
        'price_unit',
        'price_subtotal',
        'price_total',
        'qty_invoiced',
        'qty_received',
        'qty_received_manual',
        'qty_to_invoice',
        'is_downpayment',
        'planned_at',
        'product_description_variants',
        'propagate_cancel',
        'price_total_cc',
        'uom_id',
        'product_id',
        'product_packaging_id',
        'order_id',
        'partner_id',
        'currency_id',
        'company_id',
        'creator_id',
        'final_location_id',
        'order_point_id',
        'procurement_group_id',
    ];

    protected $casts = [
        'qty_received_method' => QtyReceivedMethod::class,
        'planned_at'          => 'datetime',
        'is_downpayment'      => 'boolean',
        'propagate_cancel'    => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productPackaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'purchases_order_line_taxes', 'order_line_id', 'tax_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
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

    public function accountMoveLines(): HasMany
    {
        return $this->hasMany(AccountMoveLine::class, 'purchase_order_line_id');
    }

    public function inventoryMoves(): HasMany
    {
        return $this->hasMany(InventoryMove::class, 'purchase_order_line_id');
    }

    public function moveDestinations(): BelongsToMany
    {
        return $this->belongsToMany(InventoryMove::class, 'purchases_order_line_moves', 'purchase_order_line_id', 'inventory_move_id');
    }

    public function finalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'final_location_id');
    }

    public function orderPoint(): BelongsTo
    {
        return $this->belongsTo(OrderPoint::class, 'order_point_id');
    }

    public function procurementGroup(): BelongsTo
    {
        return $this->belongsTo(ProcurementGroup::class, 'procurement_group_id');
    }

    protected static function newFactory(): OrderLineFactory
    {
        return OrderLineFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderLine) {
            $orderLine->creator_id ??= Auth::id();
        });

        static::created(function ($orderLine) {
            if ($orderLine->order->state === OrderState::PURCHASE) {
                PurchaseOrderFacade::createOrUpdateInventoryOperation(collect([$orderLine]));
            }
        });

        static::updated(function ($orderLine) {
            if ($orderLine->order->state !== OrderState::PURCHASE) {
                return;
            }

            if ($orderLine->wasChanged('product_packaging_id')) {
                $orderLine->inventoryMoves
                    ->filter(fn ($move) => ! in_array($move->state, [MoveState::CANCELED, MoveState::DONE]))
                    ->each->update(['product_packaging_id' => $orderLine->product_packaging_id]);
            }

            $previousProductQty = $orderLine->getOriginal('product_qty');

            if ($orderLine->wasChanged('price_unit')) {
                $orderLine->inventoryMoves
                    ->filter(fn ($move) => ! in_array($move->state, [MoveState::CANCELED, MoveState::DONE])
                        && $move->product_id === $orderLine->product_id
                    )
                    ->each->update(['price_unit' => $orderLine->getInventoryMovePriceUnit()]);
            }

            if (
                $orderLine->wasChanged('product_qty')
                && float_compare($previousProductQty, $orderLine->product_qty, precisionRounding: $orderLine->uom->rounding) !== 0
            ) {
                PurchaseOrderFacade::createOrUpdateInventoryOperation(collect([$orderLine]));
            }
        });
    }

    public function getInventoryMovePriceUnit(): float
    {
        $priceUnit = $this->price_unit;

        if ($this->taxes->isNotEmpty()) {
            $qty = $this->product_qty ?: 1;

            $priceUnit = TaxFacade::computeAll(
                $this->taxes,
                $priceUnit,
                currency: $this->order->currency,
                quantity: $qty,
                product: $this->product,
                partner: $this->order->partner,
                roundingMethod: 'round_globally',
            )['total_void'];

            $priceUnit = $priceUnit / $qty;
        }

        if ($this->uom->id !== $this->product->uom_id) {
            $priceUnit *= $this->uom->factor / $this->product->uom->factor;
        }

        if ($this->order->currency_id !== $this->order->company->currency_id) {
            $priceUnit = $this->order->currency->convert(
                $priceUnit,
                $this->order->company->currency,
                $this->company,
                $this->date_order ?? now()->toDateString(),
                round: false,
            );
        }

        return float_round($priceUnit, precisionDigits: 2);
    }
}
