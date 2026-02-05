<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Account\Models\Category as BaseCategory;

class Category extends BaseCategory
{
    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeFillable([
        ]);

        $this->mergeCasts([

        ]);
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_category_routes', 'category_id', 'route_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
