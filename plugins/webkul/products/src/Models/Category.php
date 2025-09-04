<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Product\Database\Factories\CategoryFactory;
use Webkul\Security\Models\User;

class Category extends Model
{
    use HasChatter, HasFactory, HasLogActivity;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'products_categories';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'full_name',
        'parent_path',
        'parent_id',
        'creator_id',
    ];

    protected $logAttributes = [
        'name',
        'full_name',
        'parent_path',
        'parent.name'  => 'Parent Category',
        'creator.name' => 'Creator',
    ];

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

        static::creating(function ($productCategory) {
            if (! static::validateNoRecursion($productCategory)) {
                throw new InvalidArgumentException('Circular reference detected in product category hierarchy');
            }

            static::handleProductCategoryData($productCategory);
        });

        static::updating(function ($productCategory) {
            if (! static::validateNoRecursion($productCategory)) {
                throw new InvalidArgumentException('Circular reference detected in product category hierarchy');
            }

            static::handleProductCategoryData($productCategory);
        });
    }

    protected static function validateNoRecursion($productCategory)
    {
        if (! $productCategory->parent_id) {
            return true;
        }

        if (
            $productCategory->exists
            && $productCategory->id == $productCategory->parent_id
        ) {
            return false;
        }

        $visitedIds = [$productCategory->exists ? $productCategory->id : -1];
        $currentParentId = $productCategory->parent_id;

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

    protected static function handleProductCategoryData($productCategory)
    {
        if ($productCategory->parent_id) {
            $parent = static::find($productCategory->parent_id);

            if ($parent) {
                $productCategory->parent_path = $parent->parent_path.$parent->id.'/';
            } else {
                $productCategory->parent_path = '/';
                $productCategory->parent_id = null;
            }
        } else {
            $productCategory->parent_path = '/';
        }

        $productCategory->full_name = static::getCompleteName($productCategory);
    }

    protected static function getCompleteName($productCategory)
    {
        $names = [];
        $names[] = $productCategory->name;

        $currentProductCategory = $productCategory;

        while ($currentProductCategory->parent_id) {
            $currentProductCategory = static::find($currentProductCategory->parent_id);

            if ($currentProductCategory) {
                array_unshift($names, $currentProductCategory->name);
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
