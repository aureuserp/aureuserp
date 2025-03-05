<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

class OrderOption extends Model
{
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
