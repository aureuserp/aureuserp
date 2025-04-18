<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Models\Tax;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Packaging;
use Webkul\Sale\Enums\OrderState;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class OrderLine extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'sales_order_lines';

    protected $fillable = [
        'sort',
        'order_id',
        'company_id',
        'currency_id',
        'order_partner_id',
        'salesman_id',
        'product_id',
        'product_uom_id',
        'linked_sale_order_sale_id',
        'creator_id',
        'state',
        'display_type',
        'virtual_id',
        'linked_virtual_id',
        'qty_delivered_method',
        'invoice_status',
        'analytic_distribution',
        'name',
        'product_uom_qty',
        'price_unit',
        'discount',
        'price_subtotal',
        'price_total',
        'price_reduce_taxexcl',
        'price_reduce_taxinc',
        'qty_delivered',
        'qty_invoiced',
        'qty_to_invoice',
        'untaxed_amount_invoiced',
        'untaxed_amount_to_invoice',
        'is_downpayment',
        'is_expense',
        'create_date',
        'write_date',
        'technical_price_unit',
        'price_tax',
        'product_qty',
        'product_packaging_qty',
        'product_packaging_id',
        'customer_lead',
        'purchase_price',
        'margin',
        'margin_percent',
    ];

    protected $casts = [
        'cast' => OrderState::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function orderPartner()
    {
        return $this->belongsTo(Partner::class, 'order_partner_id');
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'product_uom_id');
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'sales_order_line_taxes', 'order_line_id', 'tax_id');
    }

    public function productPackaging()
    {
        return $this->belongsTo(Packaging::class);
    }

    public function linkedSaleOrderSale()
    {
        return $this->belongsTo(self::class, 'linked_sale_order_sale_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
