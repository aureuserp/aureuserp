<?php

namespace Webkul\Accounting\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Account\Models\Category as BaseCategory;
use Webkul\Security\Models\User;

class Category extends BaseCategory
{
    use HasChatter;

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'product_properties_definition',
        ]);

        parent::__construct($attributes);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
