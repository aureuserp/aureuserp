<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Product\Database\Factories\CategoryFactory;
use Webkul\Security\Models\User;

class Category extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'products_categories';

    public function getModelTitle(): string
    {
        return __('products::models/category.title');
    }

    protected $fillable = [
        'name',
        'full_name',
        'parent_path',
        'parent_id',
        'creator_id',
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                 => __('products::models/category.log-attributes.name'),
            'full_name'            => __('products::models/category.log-attributes.full_name'),
            'parent_path'          => __('products::models/category.log-attributes.parent_path'),
            'parent.name'          => __('products::models/category.log-attributes.parent'),
            'creator.name'         => __('products::models/category.log-attributes.creator'),
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function priceRuleItems(): HasMany
    {
        return $this->hasMany(PriceRuleItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (! static::validateNoRecursion($category)) {
                throw new InvalidArgumentException('Circular reference detected in product category hierarchy');
            }

            $authUser = Auth::user();

            $category->creator_id ??= $authUser->id;

            static::handleProductCategoryData($category);
        });

        static::updating(function ($category) {
            if (! static::validateNoRecursion($category)) {
                throw new InvalidArgumentException('Circular reference detected in product category hierarchy');
            }

            static::handleProductCategoryData($category);
        });
    }

    protected static function validateNoRecursion($category)
    {
        if (! $category->parent_id) {
            return true;
        }

        if (
            $category->exists
            && $category->id == $category->parent_id
        ) {
            return false;
        }

        $visitedIds = [$category->exists ? $category->id : -1];
        $currentParentId = $category->parent_id;

        while ($currentParentId) {
            if (in_array($currentParentId, $visitedIds)) {
                return false;
            }

            $visitedIds[] = $currentParentId;
            $parent = static::find($currentParentId);

            if (! $parent) {
                break;
            }

            $currentParentId = $parent->parent_id;
        }

        return true;
    }

    protected static function handleProductCategoryData($category)
    {
        if ($category->parent_id) {
            $parent = static::find($category->parent_id);

            if ($parent) {
                $category->parent_path = $parent->parent_path.$parent->id.'/';
            } else {
                $category->parent_path = '/';
                $category->parent_id = null;
            }
        } else {
            $category->parent_path = '/';
        }

        $category->full_name = static::getCompleteName($category);
    }

    protected static function getCompleteName($category)
    {
        $names = [];
        $names[] = $category->name;

        $currentCategory = $category;

        while ($currentCategory->parent_id) {
            $currentCategory = static::find($currentCategory->parent_id);

            if ($currentCategory) {
                array_unshift($names, $currentCategory->name);
            } else {
                break;
            }
        }

        return implode(' / ', $names);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
