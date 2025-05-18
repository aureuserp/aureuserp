<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Models\Product;

class ProductSupplierTaxes extends Model
{
    protected $table = 'accounts_product_supplier_taxes';

    protected $fillable = [
        'product_id',
        'tax_id',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
