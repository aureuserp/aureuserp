<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Inventory\Database\Factories\PutawayRuleFactory;
use Webkul\Inventory\Enums\SubLocation;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PutawayRule extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'inventories_putaway_rules';

    protected $fillable = [
        'sub_location',
        'sort',
        'product_id',
        'category_id',
        'storage_category_id',
        'in_location_id',
        'out_location_id',
        'company_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    protected function casts(): array
    {
        return [
            'sub_location' => SubLocation::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function storageCategory(): BelongsTo
    {
        return $this->belongsTo(StorageCategory::class, 'storage_category_id');
    }

    public function inLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'in_location_id')->withTrashed();
    }

    public function outLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'out_location_id')->withTrashed();
    }

    public function packageTypes(): BelongsToMany
    {
        return $this->belongsToMany(PackageType::class, 'inventories_putaway_rule_package_types', 'putaway_rule_id', 'package_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): PutawayRuleFactory
    {
        return PutawayRuleFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($putawayRule) {
            $putawayRule->creator_id ??= Auth::id();
        });
    }
}
