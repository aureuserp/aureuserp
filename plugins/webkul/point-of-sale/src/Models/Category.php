<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PointOfSale\Database\Factories\CategoryFactory;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class Category extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'pos_categories';

    protected $fillable = [
        'name',
        'sort',
        'color',
        'image',
        'parent_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'pos_category_products', 'category_id', 'product_id');
    }

    public function configs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'pos_config_categories', 'category_id', 'config_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Category $category) {
            $category->computeCreatorId();
        });
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
