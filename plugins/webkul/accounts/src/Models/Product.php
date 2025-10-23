<?php

namespace Webkul\Account\Models;

use Webkul\Account\Models\Tax;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product as BaseProduct;

class Product extends BaseProduct
{
    use HasChatter, HasCustomFields, HasLogActivity;

    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'property_account_income_id',
            'property_account_expense_id',
            'image',
            'service_type',
            'sale_line_warn',
            'expense_policy',
            'invoice_policy',
            'sale_line_warn_msg',
            'sales_ok',
            'purchase_ok',
        ]);

        parent::__construct($attributes);
    }

    protected array $logAttributes = [
        'type',
        'name',
        'service_tracking',
        'reference',
        'barcode',
        'price',
        'cost',
        'volume',
        'weight',
        'description',
        'description_purchase',
        'description_sale',
        'enable_sales',
        'enable_purchase',
        'is_favorite',
        'is_configurable',
        'parent.name'   => 'Parent',
        'category.name' => 'Category',
        'company.name'  => 'Company',
        'creator.name'  => 'Creator',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productTaxes()
    {
        return $this->belongsToMany(Tax::class, 'accounts_product_taxes', 'product_id', 'tax_id');
    }

    public function supplierTaxes()
    {
        return $this->belongsToMany(Tax::class, 'accounts_product_supplier_taxes', 'product_id', 'tax_id');
    }
}
