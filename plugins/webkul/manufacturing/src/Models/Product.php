<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Models\Product as BaseProduct;

class Product extends BaseProduct
{
    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function billsOfMaterials(): HasMany
    {
        return $this->hasMany(BillOfMaterial::class, 'product_id');
    }

    public function billOfMaterialLines(): HasMany
    {
        return $this->hasMany(BillOfMaterialLine::class, 'product_id');
    }

    public function moves(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(Move::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(Move::class);
        }
    }

    public function moveLines(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(MoveLine::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(MoveLine::class);
        }
    }
}
