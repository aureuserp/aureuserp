<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class OrderTemplateProduct extends Model
{
    protected $table = 'sales_order_template_products';

    protected $fillable = [
        'order_template_id',
        'company_id',
        'product_id',
        'product_uom_id',
        'creator_id',
        'name',
        'quantity',
        'display_type',
    ];

    public function orderTemplate()
    {
        return $this->belongsTo(OrderTemplate::class, 'order_template_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'product_uom_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderTemplateProduct) {
            $orderTemplateProduct->company_id ??= Company::first()?->id;
            $orderTemplateProduct->product_id ??= Product::first()?->id;
            $orderTemplateProduct->product_uom_id ??= UOM::first()?->id;
            $orderTemplateProduct->creator_id ??= Auth::id();
        });
    }
}
