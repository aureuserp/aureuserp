<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Account\Models\Category as BaseCategory;
use Webkul\Chatter\Traits\HasChatter;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
