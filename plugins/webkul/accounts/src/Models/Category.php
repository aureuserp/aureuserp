<?php

namespace Webkul\Account\Models;

use Webkul\Product\Models\Category as BaseCategory;

class Category extends BaseCategory
{
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
        ]);

        parent::__construct($attributes);
    }
}
