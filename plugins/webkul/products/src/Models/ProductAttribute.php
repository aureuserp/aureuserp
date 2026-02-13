<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Product\Database\Factories\ProductAttributeFactory;
use Webkul\Security\Models\User;

class ProductAttribute extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'products_product_attributes';

    protected $fillable = [
        'sort',
        'product_id',
        'attribute_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class)->withTrashed();
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(AttributeOption::class, 'products_product_attribute_values', 'product_attribute_id', 'attribute_option_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            $attribute->creator_id ??= Auth::id();
        });

        static::deleting(function ($attribute) {
            $attribute->product->variants()->forceDelete();
        });
    }

    protected static function newFactory(): ProductAttributeFactory
    {
        return ProductAttributeFactory::new();
    }
}
