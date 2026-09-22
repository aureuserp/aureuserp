<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Product\Database\Factories\PriceListFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;

class PriceList extends Model implements Sortable
{
    use BelongsToCompany;
    use HasCustomFields, HasFactory, SortableTrait;

    protected $table = 'products_product_price_lists';

    protected $fillable = [
        'sort',
        'currency_id',
        'company_id',
        'creator_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceRuleItem::class, 'price_list_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected static function newFactory(): PriceListFactory
    {
        return PriceListFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($priceList) {
            $priceList->creator_id ??= Auth::id();
        });
    }
}
