<?php

namespace Webkul\Invoice\Models;

use Webkul\Account\Models\Tax;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
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

    public function getModelTitle(): string
    {
        return __('invoices::models/product.title');
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'type'                 => __('invoices::models/product.log-attributes.type'),
            'name'                 => __('invoices::models/product.log-attributes.name'),
            'service_tracking'     => __('invoices::models/product.log-attributes.service_tracking'),
            'reference'            => __('invoices::models/product.log-attributes.reference'),
            'barcode'              => __('invoices::models/product.log-attributes.barcode'),
            'price'                => __('invoices::models/product.log-attributes.price'),
            'cost'                 => __('invoices::models/product.log-attributes.cost'),
            'volume'               => __('invoices::models/product.log-attributes.volume'),
            'weight'               => __('invoices::models/product.log-attributes.weight'),
            'description'          => __('invoices::models/product.log-attributes.description'),
            'description_purchase' => __('invoices::models/product.log-attributes.description_purchase'),
            'description_sale'     => __('invoices::models/product.log-attributes.description_sale'),
            'enable_sales'         => __('invoices::models/product.log-attributes.enable_sales'),
            'enable_purchase'      => __('invoices::models/product.log-attributes.enable_purchase'),
            'is_favorite'          => __('invoices::models/product.log-attributes.is_favorite'),
            'is_configurable'      => __('invoices::models/product.log-attributes.is_configurable'),
            'parent.name'          => __('invoices::models/product.log-attributes.parent'),
            'category.name'        => __('invoices::models/product.log-attributes.category'),
            'company.name'         => __('invoices::models/product.log-attributes.company'),
            'creator.name'         => __('invoices::models/product.log-attributes.creator'),
        ];
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
