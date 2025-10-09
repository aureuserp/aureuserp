<?php

namespace Webkul\Accounting\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Product\Models\Category as BaseCategory;
use Webkul\Security\Models\User;

class Category extends BaseCategory
{
    use HasChatter;

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'product_properties_definition',
            'property_account_income_category_id',
            'property_account_expense_category_id',
            'property_account_down_payment_category_id',
        ]);

        parent::__construct($attributes);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
